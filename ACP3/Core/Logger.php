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
     * Logs an warning, error, etc.
     *
     * @param $channel
     * @param $level
     * @param $message
     */
    public static function log($channel, $level, $message)
    {
        if (!isset(self::$channels[$channel])) {
            $logger = new \Monolog\Logger($channel);

            $fileName = UPLOADS_DIR . 'logs/' . $channel . '-' . $level . '.log';
            $logger->pushHandler(new StreamHandler($fileName, \Monolog\Logger::NOTICE));

            self::$channels[$channel] = $logger;
        }

        /** @var \Monolog\Logger $logger */
        $logger = self::$channels[$channel];

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
} 