<?php

namespace Payjp;

class ApiRequestor
{
    private $_apiKey;

    private $_apiBase;

    private static $_httpClient;

    public function __construct($apiKey = null, $apiBase = null)
    {
        $this->_apiKey = $apiKey;
        if (!$apiBase) {
            $apiBase = Payjp::$apiBase;
        }
        $this->_apiBase = $apiBase;
    }

    private static function _encodeObjects($d)
    {
        if ($d instanceof ApiResource) {
            return Util\Util::utf8($d->id);
        } elseif ($d === true) {
            return 'true';
        } elseif ($d === false) {
            return 'false';
        } elseif (is_array($d)) {
            $res = array();
            foreach ($d as $k => $v) {
                $res[$k] = self::_encodeObjects($v);
            }
            return $res;
        } else {
            return Util\Util::utf8($d);
        }
    }

    // @var int Number of digit for randomized sleep time seconds
    private $digit_num_for_rand = 2;

    /**
     * Based on "Exponential backoff with equal jitter" algorithm.
     * https://aws.amazon.com/jp/blogs/architecture/exponential-backoff-and-jitter/
     *
     * @param int $retryCount
     *
     * @return int retry delay seconds.
     */
    private function getRetryDelay($retryCount)
    {
        $wait_half = min(Payjp::getRetryMaxDelay(), Payjp::getRetryInitialDelay() * pow(2, $retryCount)) / 2;
        $scale = pow(10, $this->digit_num_for_rand);
        return $wait_half + mt_rand(0, $wait_half * $scale) / $scale;
    }

    /**
     * @param string $method
     * @param string $url
     * @param array|null $params
     * @param array|null $headers
     *
     * @return array An array whose first element is the response and second
     *    element is the API key used to make the request.
     */
    public function request($method, $url, $params = null, $headers = null)
    {
        if (!$params) {
            $params = array();
        }
        if (!$headers) {
            $headers = array();
        }

        for ($i = 0; $i <= Payjp::getMaxRetry(); $i++) {
            list($rbody, $rcode, $myApiKey) = $this->_requestRaw($method, $url, $params, $headers);
            if ($rcode == 429 && $i != Payjp::getMaxRetry()) {
                $wait = $this->getRetryDelay($i);
                Payjp::getLogger()->info("Retry after {$wait} seconds.");
                usleep($wait * 1000000);
            } else {
                break;
            }
        }
        $resp = $this->_interpretResponse($rbody, $rcode);
        return array($resp, $myApiKey);
    }

    /**
     * @param string $rbody A JSON string.
     * @param int $rcode
     * @param array $resp
     *
     * @throws Error\InvalidRequest if the error is caused by the user.
     * @throws Error\Authentication if the error is caused by a lack of
     *    permissions.
     * @throws Error\Card if the error is the error code is 402 (payment
     *    required)
     * @throws Error\Api otherwise.
     */
    public function handleApiError($rbody, $rcode, $resp)
    {
        if (!is_array($resp) || !isset($resp['error'])) {
            $msg = "Invalid response object from API: $rbody "
              . "(HTTP response code was $rcode)";
            throw new Error\Api($msg, $rcode, $rbody, $resp);
        }

        $error = $resp['error'];
        $msg = isset($error['message']) ? $error['message'] : null;
        $param = isset($error['param']) ? $error['param'] : null;
        $code = isset($error['code']) ? $error['code'] : null;

        switch ($rcode) {
            case 400:
            case 404:
                throw new Error\InvalidRequest($msg, $param, $rcode, $rbody, $resp);
            case 401:
                throw new Error\Authentication($msg, $rcode, $rbody, $resp);
            case 402:
                throw new Error\Card($msg, $param, $code, $rcode, $rbody, $resp);
            default:
                throw new Error\Api($msg, $rcode, $rbody, $resp);
        }
    }

    private function _requestRaw($method, $url, $params, $headers)
    {
        $myApiKey = $this->_apiKey;
        if (!$myApiKey) {
            $myApiKey = Payjp::$apiKey;
        }

        if (!$myApiKey) {
            $msg = 'No API key provided.  (HINT: set your API key using '
              . '"Payjp::setApiKey(<API-KEY>)".  You can generate API keys from '
              . 'the Payjp web interface.  See https://pay.jp/api for '
              . 'details, or email support@pay.jp if you have any questions.';
            throw new Error\Authentication($msg);
        }

        $absUrl = $this->_apiBase.$url;
        $params = self::_encodeObjects($params);
        $langVersion = phpversion();
        $uname = php_uname();
        $ua = array(
            'bindings_version' => Payjp::VERSION,
            'lang' => 'php',
            'lang_version' => $langVersion,
            'publisher' => 'payjp',
            'uname' => $uname,
        );
        $defaultHeaders = array(
            'X-Payjp-Client-User-Agent' => json_encode($ua),
            'User-Agent' => 'Payjp/v1 PhpBindings/' . Payjp::VERSION,
                'Authorization' => 'Basic ' . base64_encode($myApiKey.':')
        );
        if (Payjp::$apiVersion) {
            $defaultHeaders['Payjp-Version'] = Payjp::$apiVersion;
        }
        $hasFile = false;
        $hasCurlFile = class_exists('\CURLFile', false);
        foreach ($params as $k => $v) {
            if (is_resource($v)) {
                $hasFile = true;
                $params[$k] = self::_processResourceParam($v, $hasCurlFile);
            } elseif ($hasCurlFile && $v instanceof \CURLFile) {
                $hasFile = true;
            }
        }

        if ($hasFile) {
            $defaultHeaders['Content-Type'] = 'multipart/form-data';
        } else {
            $defaultHeaders['Content-Type'] = 'application/x-www-form-urlencoded';
        }

        $combinedHeaders = array_merge($defaultHeaders, $headers);
        $rawHeaders = array();

        foreach ($combinedHeaders as $header => $value) {
            $rawHeaders[] = $header . ': ' . $value;
        }

        list($rbody, $rcode) = $this->httpClient()->request(
            $method,
            $absUrl,
            $rawHeaders,
            $params,
            $hasFile
        );
        return array($rbody, $rcode, $myApiKey);
    }

    private function _processResourceParam($resource, $hasCurlFile)
    {
        if (get_resource_type($resource) !== 'stream') {
            throw new Error\Api(
                'Attempted to upload a resource that is not a stream'
            );
        }

        $metaData = stream_get_meta_data($resource);
        if ($metaData['wrapper_type'] !== 'plainfile') {
            throw new Error\Api(
                'Only plainfile resource streams are supported'
            );
        }

        if ($hasCurlFile) {
            // We don't have the filename or mimetype, but the API doesn't care
            return new \CURLFile($metaData['uri']);
        } else {
            return '@'.$metaData['uri'];
        }
    }

    private function _interpretResponse($rbody, $rcode)
    {
        try {
            $resp = json_decode($rbody, true);
        } catch (Exception $e) {
            $msg = "Invalid response body from API: $rbody "
              . "(HTTP response code was $rcode)";
            throw new Error\Api($msg, $rcode, $rbody);
        }

        if ($rcode < 200 || $rcode >= 300) {
            $this->handleApiError($rbody, $rcode, $resp);
        }
        return $resp;
    }

    public static function setHttpClient($client)
    {
        self::$_httpClient = $client;
    }

    private function httpClient()
    {
        if (!self::$_httpClient) {
            self::$_httpClient = HttpClient\CurlClient::instance();
        }
        return self::$_httpClient;
    }
}
