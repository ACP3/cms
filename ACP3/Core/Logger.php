<?php

namespace ACP3\Core;

use ACP3\Core\Environment\ApplicationPath;
use Monolog\Formatter\LineFormatter;
use Monolog\Handler\StreamHandler;
use Psr\Log\LogLevel;

/**
 * Class for logging warnings, errors, etc.
 *
 * @package ACP3\Core
 */
class Logger
{
    /**
     * @var \ACP3\Core\Environment\ApplicationPath
     */
    protected $appPath;
    /**
     * Contains all already set log channels
     * @var \Monolog\Logger[]
     */
    private $channels = [];

    /**
     * Logger constructor.
     *
     * @param \ACP3\Core\Environment\ApplicationPath $appPath
     */
    public function __construct(ApplicationPath $appPath)
    {
        $this->appPath = $appPath;
    }

    /**
     * Wrapper method for logging notices, warnings, errors, etc.
     *
     * @param string $channel
     * @param string $level
     * @param mixed  $message
     * @param array  $context
     */
    private function log($channel, $level, $message, array $context = [])
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
     * Debug log
     *
     * @param string $channel
     * @param mixed  $message
     * @param array  $context
     */
    public function debug($channel, $message, array $context = [])
    {
        $this->log($channel, LogLevel::DEBUG, $message, $context);
    }

    /**
     * Info log
     *
     * @param string $channel
     * @param mixed  $message
     * @param array  $context
     */
    public function info($channel, $message, array $context = [])
    {
        $this->log($channel, LogLevel::INFO, $message, $context);
    }

    /**
     * Notice log
     *
     * @param string $channel
     * @param mixed  $message
     * @param array  $context
     */
    public function notice($channel, $message, array $context = [])
    {
        $this->log($channel, LogLevel::NOTICE, $message, $context);
    }

    /**
     * Warning log
     *
     * @param string $channel
     * @param mixed  $message
     * @param array  $context
     */
    public function warning($channel, $message, array $context = [])
    {
        $this->log($channel, LogLevel::NOTICE, $message, $context);
    }

    /**
     * Error log
     *
     * @param string $channel
     * @param mixed  $message
     * @param array  $context
     */
    public function error($channel, $message, array $context = [])
    {
        $this->log($channel, LogLevel::ERROR, $message, $context);
    }

    /**
     * Critical log
     *
     * @param string $channel
     * @param mixed  $message
     * @param array  $context
     */
    public function critical($channel, $message, array $context = [])
    {
        $this->log($channel, LogLevel::CRITICAL, $message, $context);
    }

    /**
     * Alert log
     *
     * @param string $channel
     * @param mixed  $message
     * @param array  $context
     */
    public function alert($channel, $message, array $context = [])
    {
        $this->log($channel, LogLevel::ALERT, $message, $context);
    }

    /**
     * Emergency log
     *
     * @param string $channel
     * @param mixed  $message
     * @param array  $context
     */
    public function emergency($channel, $message, array $context = [])
    {
        $this->log($channel, LogLevel::EMERGENCY, $message, $context);
    }

    /**
     * @param string $channel
     * @param string $level
     */
    private function createChannel($channel, $level)
    {
        $fileName = $this->appPath->getCacheDir() . 'logs/' . $channel . '.log';
        $logLevelConst = constant(\Monolog\Logger::class . '::' . strtoupper($level));

        $stream = new StreamHandler($fileName, $logLevelConst);
        $stream->setFormatter(new LineFormatter(null, null, true));

        $this->channels[$channel] = new \Monolog\Logger($channel, [$stream]);
    }

    /**
     * @param $message
     *
     * @return string
     */
    private function prettyPrintMessage($message)
    {
        if (is_array($message) || is_object($message)) {
            $message = var_export($message, true);
        }

        return $message;
    }
}
