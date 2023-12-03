<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace RFM\Facade;

use function RFM\request;

class Input
{
    /**
     * Get an item from the input data.
     *
     * This method is used for all request verbs (GET, POST, PUT, and DELETE)
     *
     * @param string $key
     */
    public static function get($key = null, $default = null)
    {
        return request()->get($key, $default);
    }
}
