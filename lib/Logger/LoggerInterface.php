<?php

namespace Payjp\Logger;

interface LoggerInterface
{
    /**
     * @param string $message
     */
    public function log($message);
}
