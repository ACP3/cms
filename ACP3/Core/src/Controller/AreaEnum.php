<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Core\Controller;

enum AreaEnum: string
{
    case AREA_ADMIN = 'admin';
    case AREA_FRONTEND = 'frontend';
    case AREA_INSTALL = 'installer';
    case AREA_WIDGET = 'widget';
    /**
     * @return string[]
     *
     * @throws \ReflectionException
     */
    public static function getAreas(): array
    {
        return array_column(self::cases(), 'value');
    }
}
