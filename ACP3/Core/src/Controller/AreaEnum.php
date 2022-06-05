<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Core\Controller;

use ACP3\Core\Enum\EnumValuesTrait;

enum AreaEnum: string
{
    use EnumValuesTrait;

    case AREA_ADMIN = 'admin';
    case AREA_FRONTEND = 'frontend';
    case AREA_INSTALL = 'installer';
    case AREA_WIDGET = 'widget';
    /**
     * @return string[]
     *
     * @deprecated since ACP3 version 6.1.0, to be removed with version 7.0.0. Use ::values() instead.
     */
    public static function getAreas(): array
    {
        return self::values();
    }
}
