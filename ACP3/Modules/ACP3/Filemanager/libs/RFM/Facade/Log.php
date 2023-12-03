<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace RFM\Facade;

use function RFM\logger;

class Log
{
    /**
     * Logs an informational message to the log.
     *
     * @param string $message
     */
    public static function info($message)
    {
        logger()->log($message);
    }
}
