<?php

namespace Payjp;

class Payjp
{
    // @var string The Payjp API key to be used for requests.
    public static $apiKey;

    // @var string The base URL for the Payjp API.
    public static $apiBase = 'https://api.pay.jp';

    // @var string|null The version of the Payjp API to use for requests.
    public static $apiVersion = null;

    // @var boolean Defaults to true.
    public static $verifySslCerts = true;

    // @var \Payjp\Logger\LoggerInterface Default logger will output to error_log().
    public static $logger = null;

    // @var int Max retry count for over_capacity 429 response.
    public static $maxRetry = 0;

    /**
     * The retry interval base value for over_capacity 429 response.
     * Based on "Exponential backoff with equal jitter" algorithm.
     * See https://aws.amazon.com/jp/blogs/architecture/exponential-backoff-and-jitter/
     *
     * @var int
     */
    public static $retryInitialDelay = 2;

    // @var int Max retry delay seconds for over_capacity 429 response.
    public static $retryMaxDelay = 32;

    const VERSION = '1.0.0';

    /**
     * @return string The API key used for requests.
     */
    public static function getApiKey()
    {
        return self::$apiKey;
    }

    /**
     * Sets the API key to be used for requests.
     *
     * @param string $apiKey
     */
    public static function setApiKey($apiKey)
    {
        self::$apiKey = $apiKey;
    }

    /**
     * @return string The API version used for requests. null if we're using the
     *    latest version.
     */
    public static function getApiVersion()
    {
        return self::$apiVersion;
    }

    /**
     * @param string $apiVersion The API version to use for requests.
     */
    public static function setApiVersion($apiVersion)
    {
        self::$apiVersion = $apiVersion;
    }

    /**
     * @return boolean
     */
    public static function getVerifySslCerts()
    {
        return self::$verifySslCerts;
    }

    /**
     * @param boolean $verify
     */
    public static function setVerifySslCerts($verify)
    {
        self::$verifySslCerts = $verify;
    }

    /**
     * @return \Payjp\Logger\LoggerInterface
     */
    public static function getLogger()
    {
        if (self::$logger == null) {
            return new \Payjp\Logger\DefaultLogger();
        }
        return self::$logger;
    }

    /**
     * @param \Payjp\Logger\LoggerInterface $logger
     */
    public static function setLogger($logger)
    {
        self::$logger = $logger;
    }
    /**
     * @return int
     */
    public static function getMaxRetry()
    {
        return self::$maxRetry;
    }

    /**
     * @param int $value
     */
    public static function setMaxRetry($value)
    {
        self::$maxRetry = $value;
    }

    /**
     * @return int
     */
    public static function getRetryInitialDelay()
    {
        return self::$retryInitialDelay;
    }

    /**
     * @param int $value
     */
    public static function setRetryInitialDelay($value)
    {
        self::$retryInitialDelay = $value;
    }

    /**
     * @return int
     */
    public static function getRetryMaxDelay()
    {
        return self::$retryMaxDelay;
    }

    /**
     * @param int $value
     */
    public static function setRetryMaxDelay($value)
    {
        self::$retryMaxDelay = $value;
    }
}
