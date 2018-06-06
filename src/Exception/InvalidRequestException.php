<?php

namespace Payjp\Exception;

/**
 * Class InvalidRequestException
 * @package Payjp\Exception
 */
class InvalidRequestException extends BaseException
{
    /**
     * @var string $params
     */
    protected $param;

    /**
     * InvalidRequestException constructor.
     * @param $message
     * @param $param
     * @param int|null $httpStatus
     * @param \GuzzleHttp\Psr7\Stream|null $httpBody
     * @param array|null $jsonBody
     * @param \Exception|null $previous
     */
    public function __construct($message, $param, $httpStatus = null, $httpBody = null, $jsonBody = null, $previous = null) {
        parent::__construct($message, $httpStatus, $httpBody, $previous);
        $this->param = $param;
    }
}
