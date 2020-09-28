<?php

namespace Payjp\Logger;

/**
 * PSR-3 logger instance minimal implements. see https://www.php-fig.org/psr/psr-3/
 */
interface LoggerInterface
{
    /**
     * Runtime errors that do not require immediate action but should typically
     * be logged and monitored.
     *
     * @param string $message
     * @param array $context
     * @return void
     */
    public function error($message, array $context = array());

    /**
     * Interesting events.
     *
     * Example: User logs in, SQL logs.
     *
     * @param string $message
     * @param array $context
     * @return void
     */
    public function info($message, array $context = array());
}
