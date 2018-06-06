<?php

namespace Payjp\Exception;

use Exception;
use GuzzleHttp\Psr7\Stream;

/**
 * Class BaseException
 * @package Payjp\Exception
 */
abstract class BaseException extends Exception
{
    /**
     * @var int $httpStatus
     */
    protected $httpStatus;

    /**
     * @var \GuzzleHttp\Psr7\Stream|null
     */
    protected $httpBody;

    /**
     * @var array|null
     */
    protected $jsonBody;

    /**
     * BaseException constructor.
     * @param string $message
     * @param int $httpStatus
     * @param \GuzzleHttp\Psr7\Stream $httpBody
     * @param array|null $jsonBody
     * @param Exception|null $previous
     */
    public function __construct($message, $httpStatus, $httpBody, array $jsonBody = null, \Exception $previous = null) {
        parent::__construct($message, 1, $previous);
        $this->httpStatus = $httpStatus;
        $this->httpBody = $httpBody;
        $this->jsonBody = $jsonBody;
    }

    /**
     * @return int
     */
    public function getHttpStatus()
    {
        return $this->httpStatus;
    }

    /**
     * @return \GuzzleHttp\Psr7\Stream
     */
    public function getHttpBody()
    {
        return $this->httpBody;
    }

    /**
     * @return array|null
     */
    public function getJsonBody()
    {
        return $this->jsonBody;
    }
}
