<?php

namespace Payjp\Error;

class Card extends Base
{
    public $param;

    public function __construct(
        $message,
        $param,
        $code,
        $httpStatus,
        $httpBody,
        $jsonBody
    ) {
        parent::__construct($message, $httpStatus, $httpBody, $jsonBody);
        $this->param = $param;
        $this->code = $code;
    }
}
