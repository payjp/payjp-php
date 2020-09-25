<?php

namespace Payjp\Logger;

class DefaultLogger implements LoggerInterface
{
    public function log($message)
    {
        error_log($message);
    }
}
