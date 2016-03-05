<?php

namespace ACP3\Core;

use ACP3\Core\Environment\ApplicationPath;
use Monolog\Formatter\LineFormatter;
use Monolog\Handler\StreamHandler;

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

        /** @var \Monolog\Logger $logger */
        $logger = $this->channels[$channel];

        switch ($level) {
            case 'debug':
                $logger->debug($this->prettyPrintMessage($message), $context);
                break;
            case 'info':
                $logger->info($message, $context);
                break;
            case 'notice':
                $logger->notice($message, $context);
                break;
            case 'warning':
                $logger->warning($message, $context);
                break;
            case 'error':
                $logger->error($message, $context);
                break;
            case 'critical':
                $logger->critical($message, $context);
                break;
            case 'alert':
                $logger->alert($message, $context);
                break;
            case 'emergency':
                $logger->emergency($message, $context);
                break;
        }
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
        $this->log($channel, 'debug', $message, $context);
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
        $this->log($channel, 'info', $message, $context);
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
        $this->log($channel, 'notice', $message, $context);
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
        $this->log($channel, 'warning', $message, $context);
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
        $this->log($channel, 'error', $message, $context);
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
        $this->log($channel, 'critical', $message, $context);
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
        $this->log($channel, 'alert', $message, $context);
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
        $this->log($channel, 'emergency', $message, $context);
    }

    /**
     * @param string $channel
     * @param string $level
     */
    private function createChannel($channel, $level)
    {
        $fileName = $this->appPath->getCacheDir() . 'logs/' . $channel . '.log';
        $logLevelConst = constant('\Monolog\Logger::' . strtoupper($level));

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
