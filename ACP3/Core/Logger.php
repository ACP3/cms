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
     * @var array
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
     */
    private function log($channel, $level, $message)
    {
        $channelName = $channel . '-' . $level;

        if (!isset($this->channels[$channelName])) {
            $logger = new \Monolog\Logger($channelName);

            $fileName = $this->appPath->getCacheDir() . 'logs/' . $channelName . '.log';
            $logLevelConst = constant('\Monolog\Logger::' . strtoupper($level));
            $stream = new StreamHandler($fileName, $logLevelConst);
            $stream->setFormatter(new LineFormatter(null, null, true));

            $logger->pushHandler($stream);

            $this->channels[$channelName] = $logger;
        }

        /** @var \Monolog\Logger $logger */
        $logger = $this->channels[$channelName];

        switch ($level) {
            case 'debug':
                if (is_array($message) || is_object($message)) {
                    $message = var_export($message, true);
                }

                $logger->debug($message);
                break;
            case 'info':
                $logger->info($message);
                break;
            case 'notice':
                $logger->notice($message);
                break;
            case 'warning':
                $logger->warning($message);
                break;
            case 'error':
                $logger->error($message);
                break;
            case 'critical':
                $logger->critical($message);
                break;
            case 'alert':
                $logger->alert($message);
                break;
            case 'emergency':
                $logger->emergency($message);
                break;
        }
    }

    /**
     * Debug log
     *
     * @param string $channel
     * @param mixed  $message
     */
    public function debug($channel, $message)
    {
        $this->log($channel, 'debug', $message);
    }

    /**
     * Info log
     *
     * @param string $channel
     * @param mixed  $message
     */
    public function info($channel, $message)
    {
        $this->log($channel, 'info', $message);
    }

    /**
     * Notice log
     *
     * @param string $channel
     * @param mixed  $message
     */
    public function notice($channel, $message)
    {
        $this->log($channel, 'notice', $message);
    }

    /**
     * Warning log
     *
     * @param string $channel
     * @param mixed  $message
     */
    public function warning($channel, $message)
    {
        $this->log($channel, 'warning', $message);
    }

    /**
     * Error log
     *
     * @param string $channel
     * @param mixed  $message
     */
    public function error($channel, $message)
    {
        $this->log($channel, 'error', $message);
    }

    /**
     * Critical log
     *
     * @param string $channel
     * @param mixed  $message
     */
    public function critical($channel, $message)
    {
        $this->log($channel, 'critical', $message);
    }

    /**
     * Alert log
     *
     * @param string $channel
     * @param mixed  $message
     */
    public function alert($channel, $message)
    {
        $this->log($channel, 'alert', $message);
    }

    /**
     * Emergency log
     *
     * @param string $channel
     * @param mixed  $message
     */
    public function emergency($channel, $message)
    {
        $this->log($channel, 'emergency', $message);
    }
}
