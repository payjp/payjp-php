<?php

namespace Payjp;

use Exception;
use GuzzleHttp\Client as GuzzleClient;
use Payjp\Exception\ApiConnectionException;
use Payjp\Exception\ApiException;
use Payjp\Exception\AuthenticationException;
use Payjp\Exception\CardException;
use Payjp\Exception\InvalidRequestException;

/**
 * Class Client
 * @package Payjp
 */
class Client
{
    const VERSION = 2.0;

    /**
     * @var string $apiKey
     */
    private $apiKey;

    /**
     * @var array $config
     */
    private $config;

    /**
     * @var Account
     */
    public $accounts;

    /**
     * @var Charge
     */
    public $charges;

    /**
     * @var Customer
     */
    public $customers;

    /**
     * @var Plan
     */
    public $plans;

    /**
     * @var Token
     */
    public $tokens;

    /**
     * @var Transfer
     */
    public $transfers;

    /**
     * @var Event
     */
    public $events;

    /**
     * @var Subscription
     */
    public $subscriptions;

    /**
     * Payjp constructor.
     * @param string $apiKey
     * @param array $config
     * @throws Exception
     */
    public function __construct($apiKey, array $config = [])
    {
        if (!isset($apiKey)) {
            throw new Exception("Please set apikey.", 1);
        }

        $this->apiKey = $apiKey;
        $this->config = $config;

        $this->accounts = new Account($this);
        $this->charges = new Charge($this);
        $this->customers = new Customer($this);
        $this->plans = new Plan($this);
        $this->tokens = new Token($this);
        $this->transfers = new Transfer($this);
        $this->events = new Event($this);
        $this->subscriptions = new Subscription($this);
    }

    /**
     * @return mixed|string
     */
    private function getApiBase()
    {
        $apiBase = isset($this->config['api_base']) ? $this->config['api_base'] : 'https://api.pay.jp/v1/';
        if (substr($apiBase, -1) !== '/') {
            $apiBase .= '/';
        }
        return $apiBase;
    }

    /**
     * @param string $method
     * @return array
     */
    private function getHeaders($method) {
        $langVersion = phpversion();
        $uname = php_uname();
        $ua = [
            'bindings_version' => Client::VERSION,
            'lang' => 'php',
            'lang_version' => $langVersion,
            'publisher' => 'payjp',
            'uname' => $uname,
            'httplib' => 'Guzzle',
            'httplib_version' => GuzzleClient::VERSION,
        ];

        $headers = [
            'Accept' => 'application/json',
            'X-Payjp-Client-User-Agent' => json_encode($ua),
            'User-Agent' => 'Payjp/v2 PhpBindings/' . Client::VERSION,
        ];

        if ($method === 'POST') {
            $headers['Content-Type'] = 'application/x-www-form-urlencoded';
        }

        return $headers;
    }

    /**
     * @param string $endpoint
     * @return string
     */
    private function getUrl($endpoint)
    {
        return sprintf('%s%s', $this->getApiBase(), $endpoint);
    }

    /**
     * @return bool|mixed
     * @throws Exception
     */
    private function isArrayResponse()
    {
        $isArray = isset($this->config['response_as_array']) ? $this->config['response_as_array'] : true;
        if (!is_bool($isArray)) {
            throw new Exception('response_as_array accepts only bool');
        }
        return $isArray;
    }

    /**
     * @param array $query
     * @return array
     */
    private function buildQuery(array $query)
    {
        if (!empty($query)) {
            foreach (['card', 'metadata'] as $target) {
                if (isset($query[$target]) && is_array($query[$target])) {
                    foreach ($query[$target] as $key => $value) {
                        $query[sprintf('%s[%s]', $target, $key)] = $value;
                    }
                    unset($query[$target]);
                }
            }
        }
        return $query;
    }


    /**
     * @param $method
     * @param $path
     * @param array $query
     * @param array $json
     * @return mixed|null|\Psr\Http\Message\ResponseInterface
     * @throws ApiConnectionException
     * @throws ApiException
     * @throws AuthenticationException
     * @throws CardException
     * @throws InvalidRequestException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function request($method, $path, array $query = [], array $json = [])
    {
        $client = new GuzzleClient([
            'base_uri' => $this->getApiBase(),
            'timeout'  => 2.0,
        ]);

        $payload = [];

        $payload['auth'] = [$this->apiKey, ''];
        $payload['headers'] = $this->getHeaders($method);

        if (!empty($json)) {
            $payload['json'] = $json;
        } else if ($method === 'GET') {
            $payload['query'] = $this->buildQuery($query);
        } else if ($method === 'POST') {
            $payload['form_params'] = $this->buildQuery($query);
        }

        try {
            $response = $client->request($method, $path, $payload);
        } catch (\GuzzleHttp\Exception\ConnectException $e) {
            throw new ApiConnectionException(
                "Unexpected error communicating with PAY.JP. "
                . "If this problem persists, let us know at support@pay.jp\n",
                null, null, null, $e
            );
        } catch (\GuzzleHttp\Exception\ClientException $e) {
            $response = $e->getResponse();
        }

        $body = $response->getBody();
        $statusCode = $response->getStatusCode();

        try {
            $response = json_decode($body, $this->isArrayResponse());
        } catch (Exception $e) {
            $msg = "Invalid response body from API: $body "
              . "(HTTP response code was $statusCode)";
            throw new ApiException($msg, $statusCode, $body, null, $e);
        }

        if ($statusCode < 200 || $statusCode >= 300) {
            $this->handleApiError($body, $statusCode, $response);
        }
        return $response;
    }

    /**
     * @param $body
     * @param $statusCode
     * @param $response
     * @throws ApiException
     * @throws AuthenticationException
     * @throws CardException
     * @throws InvalidRequestException
     */
    private function handleApiError($body, $statusCode, $response)
    {
        if (!is_array($response) || !isset($response['error'])) {
            $msg = "Invalid response object from API: $body "
              . "(HTTP response code was $statusCode)";
            throw new ApiException($msg, $statusCode, $body, $response);
        }

        $error = $response['error'];
        $msg = isset($error['message']) ? $error['message'] : null;
        $param = isset($error['param']) ? $error['param'] : null;
        $code = isset($error['code']) ? $error['code'] : null;

        switch ($statusCode) {
            case 400:
            case 404:
                throw new InvalidRequestException($msg, $param, $statusCode, $body, $response);
            case 401:
                throw new AuthenticationException($msg, $statusCode, $body, $response);
            case 402:
                throw new CardException($msg, $param, $code, $statusCode, $body, $response);
            default:
                throw new ApiException($msg, $statusCode, $body, $response);
        }
    }
}
