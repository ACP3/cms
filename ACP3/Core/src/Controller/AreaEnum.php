<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Core\Controller;

use ACP3\Core\Enum\BaseEnum;

class AreaEnum extends BaseEnum
{
    public const AREA_ADMIN = 'admin';
    public const AREA_FRONTEND = 'frontend';
    public const AREA_INSTALL = 'installer';
    public const AREA_WIDGET = 'widget';

    /**
     * @throws \ReflectionException
     */
    public static function getAreas(): array
    {
        return self::getConstants();
    }
}
