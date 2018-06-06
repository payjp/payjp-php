<?php

namespace Payjp\Exception;

/**
 * Class RateLimit
 * @package Payjp\Exception
 */
class RateLimitException extends InvalidRequestException
{
    /**
     * RateLimitException constructor.
     * @param string $message
     * @param string $param
     * @param int $httpStatus
     * @param \GuzzleHttp\Psr7\Stream $httpBody
     * @param \Exception|null $previous
     */
    public function __construct($message, $param, $httpStatus, $httpBody, $previous = null) {
        parent::__construct($message, $httpStatus, $httpBody, $previous);
    }
}
