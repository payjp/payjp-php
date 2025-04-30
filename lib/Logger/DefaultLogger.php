<?php

namespace Payjp\Logger;

class DefaultLogger implements LoggerInterface
{
    public function error($message, array $context = [])
    {
        error_log($message);
    }

    public function info($message, array $context = [])
    {
        // if you need any output, please use \Payjp\Payjp::setLogger
    }
}
