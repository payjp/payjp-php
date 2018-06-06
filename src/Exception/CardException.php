<?php

namespace Payjp\Exception;

/**
 * Class CardException
 * @package Payjp\Exception
 */
class CardException extends BaseException
{
    /**
     * @var string
     */
    protected $param;

    /**
     * @var string
     */
    protected $code;

    /**
     * CardException constructor.
     * @param string $message
     * @param string $param
     * @param string $code
     * @param int $httpStatus
     * @param \GuzzleHttp\Psr7\Stream $httpBody
     * @param array $jsonBody
     */
    public function __construct($message, $param, $code, $httpStatus, $httpBody, $jsonBody) {
        parent::__construct($message, $httpStatus, $httpBody, $jsonBody);
        $this->param = $param;
        $this->code = $code;
    }
}
