<?php

namespace ACP3\Core;

use Monolog\Handler\StreamHandler;

/**
 * Class for logging warnings, errors, etc.
 *
 * @package ACP3\Core
 */
class Logger
{
    /**
     * Contains all already set log channels
     * @var array
     */
    private static $channels = array();

    /**
     * Wrapper method for logging notices, warnings, errors, etc.
     *
     * @param $channel
     * @param $level
     * @param $message
     */
    private static function _log($channel, $level, $message)
    {
        $channelName = $channel . '-' . $level;

        if (!isset(self::$channels[$channelName])) {
            $logger = new \Monolog\Logger($channelName);

            $fileName = UPLOADS_DIR . 'logs/' . $channelName . '.log';
            $logLevelConst = constant('\Monolog\Logger::' . strtoupper($level));
            $logger->pushHandler(new StreamHandler($fileName, $logLevelConst));

            self::$channels[$channelName] = $logger;
        }

        /** @var \Monolog\Logger $logger */
        $logger = self::$channels[$channelName];

        switch ($level) {
            case 'debug':
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
     * @param $channel
     * @param $message
     */
    public static function debug($channel, $message)
    {
        self::_log($channel, 'debug', $message);
    }

    /**
     * Info log
     *
     * @param $channel
     * @param $message
     */
    public static function info($channel, $message)
    {
        self::_log($channel, 'info', $message);
    }

    /**
     * Notice log
     *
     * @param $channel
     * @param $message
     */
    public static function notice($channel, $message)
    {
        self::_log($channel, 'notice', $message);
    }

    /**
     * Warning log
     *
     * @param $channel
     * @param $message
     */
    public static function warning($channel, $message)
    {
        self::_log($channel, 'warning', $message);
    }

    /**
     * Error log
     *
     * @param $channel
     * @param $message
     */
    public static function error($channel, $message)
    {
        self::_log($channel, 'error', $message);
    }

    /**
     * Critical log
     *
     * @param $channel
     * @param $message
     */
    public static function critical($channel, $message)
    {
        self::_log($channel, 'critical', $message);
    }

    /**
     * Alert log
     *
     * @param $channel
     * @param $message
     */
    public static function alert($channel, $message)
    {
        self::_log($channel, 'alert', $message);
    }

    /**
     * Emergency log
     *
     * @param $channel
     * @param $message
     */
    public static function emergency($channel, $message)
    {
        self::_log($channel, 'emergency', $message);
    }

}