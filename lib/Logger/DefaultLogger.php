<?php

namespace Payjp\Logger;

class DefaultLogger implements LoggerInterface
{
    public function error($message, array $context = array())
    {
        error_log($message);
    }

    public function info($message, array $context = array())
    {
        error_log($message);
    }
}
