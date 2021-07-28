<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Core\Logger;

use ACP3\Core\Environment\ApplicationPath;
use Monolog\Formatter\LineFormatter;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use Psr\Log\LoggerInterface;
use Psr\Log\LogLevel;

class LoggerFactory
{
    /**
     * @var ApplicationPath
     */
    private $appPath;

    /**
     * LoggerFactory constructor.
     */
    public function __construct(ApplicationPath $appPath)
    {
        $this->appPath = $appPath;
    }

    /**
     * @param LogLevel::* $level
     *
     * @throws \Exception
     */
    public function create(string $channel, string $level = LogLevel::WARNING): LoggerInterface
    {
        $fileName = $this->appPath->getCacheDir() . 'logs/' . $channel . '.log';

        $stream = new StreamHandler($fileName, $level);
        $stream->setFormatter(new LineFormatter(null, null, true));

        return new Logger($channel, [$stream]);
    }
}
