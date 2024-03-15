<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Core\Logger;

use ACP3\Core\Environment\ApplicationMode;
use ACP3\Core\Environment\ApplicationPath;
use Monolog\Formatter\LineFormatter;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use Psr\Log\LoggerInterface;
use Psr\Log\LogLevel;

class LoggerFactory
{
    public function __construct(private readonly ApplicationPath $appPath)
    {
    }

    /**
     * @param LogLevel::*|null $level Default to LogLevel::DEBUG, if no level has been set explicitly
     *
     * @throws \Exception
     */
    public function create(string $channel, ?string $level = null): LoggerInterface
    {
        $fileName = $this->appPath->getCacheDir() . 'logs/' . $channel . '.log';

        if ($level === null) {
            $level = $this->appPath->getApplicationMode() === ApplicationMode::PRODUCTION ? LogLevel::WARNING : LogLevel::DEBUG;
        }

        $stream = new StreamHandler($fileName, $level);
        $stream->setFormatter(new LineFormatter(null, null, true));

        return new Logger($channel, [$stream]);
    }
}
