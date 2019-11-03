<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Core;

use ACP3\Core\Environment\ApplicationPath;
use Monolog\Formatter\LineFormatter;
use Monolog\Handler\StreamHandler;
use Psr\Log\LogLevel;

/**
 * Class for logging warnings, errors, etc.
 *
 * @deprecated since version 4.9.0, to be removed with version 5.0.0. Please use the \ACP3\Core\Logger\LoggerFactory instead
 */
class Logger
{
    /**
     * @var \ACP3\Core\Environment\ApplicationPath
     */
    protected $appPath;
    /**
     * Contains all already set log channels.
     *
     * @var \Monolog\Logger[]
     */
    private $channels = [];

    public function __construct(ApplicationPath $appPath)
    {
        $this->appPath = $appPath;
    }

    /**
     * Wrapper method for logging notices, warnings, errors, etc.
     *
     * @param mixed $message
     *
     * @throws \Exception
     */
    private function log(string $channel, string $level, $message, array $context = []): void
    {
        if (!isset($this->channels[$channel])) {
            $this->createChannel($channel, $level);
        }

        if ($level === LogLevel::DEBUG) {
            $message = $this->prettyPrintMessage($message);
        }

        $this->channels[$channel]->{$level}($message, $context);
    }

    /**
     * @throws \Exception
     */
    private function createChannel(string $channel, string $level): void
    {
        $fileName = $this->appPath->getCacheDir() . 'logs/' . $channel . '.log';
        $logLevelConst = \constant(\Monolog\Logger::class . '::' . \strtoupper($level));

        $stream = new StreamHandler($fileName, $logLevelConst);
        $stream->setFormatter(new LineFormatter(null, null, true));

        $this->channels[$channel] = new \Monolog\Logger($channel, [$stream]);
    }

    /**
     * @param mixed $message
     */
    private function prettyPrintMessage($message): string
    {
        if (\is_array($message) || \is_object($message)) {
            $message = \var_export($message, true);
        }

        return $message;
    }

    /**
     * Debug log.
     *
     * @param string $channel
     * @param mixed  $message
     *
     * @throws \Exception
     */
    public function debug($channel, $message, array $context = [])
    {
        $this->log($channel, LogLevel::DEBUG, $message, $context);
    }

    /**
     * Info log.
     *
     * @param string $channel
     * @param mixed  $message
     *
     * @throws \Exception
     */
    public function info($channel, $message, array $context = [])
    {
        $this->log($channel, LogLevel::INFO, $message, $context);
    }

    /**
     * Notice log.
     *
     * @param string $channel
     * @param mixed  $message
     *
     * @throws \Exception
     */
    public function notice($channel, $message, array $context = [])
    {
        $this->log($channel, LogLevel::NOTICE, $message, $context);
    }

    /**
     * Warning log.
     *
     * @param string $channel
     * @param mixed  $message
     *
     * @throws \Exception
     */
    public function warning($channel, $message, array $context = [])
    {
        $this->log($channel, LogLevel::NOTICE, $message, $context);
    }

    /**
     * Error log.
     *
     * @param string $channel
     * @param mixed  $message
     *
     * @throws \Exception
     */
    public function error($channel, $message, array $context = [])
    {
        $this->log($channel, LogLevel::ERROR, $message, $context);
    }

    /**
     * Critical log.
     *
     * @param string $channel
     * @param mixed  $message
     *
     * @throws \Exception
     */
    public function critical($channel, $message, array $context = [])
    {
        $this->log($channel, LogLevel::CRITICAL, $message, $context);
    }

    /**
     * Alert log.
     *
     * @param string $channel
     * @param mixed  $message
     *
     * @throws \Exception
     */
    public function alert($channel, $message, array $context = [])
    {
        $this->log($channel, LogLevel::ALERT, $message, $context);
    }

    /**
     * Emergency log.
     *
     * @param string $channel
     * @param mixed  $message
     *
     * @throws \Exception
     */
    public function emergency($channel, $message, array $context = [])
    {
        $this->log($channel, LogLevel::EMERGENCY, $message, $context);
    }
}
