<?php
/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licencing details.
 */

namespace ACP3\Core\Controller;

use ACP3\Core\Enum\BaseEnum;

class AreaEnum extends BaseEnum
{
    const AREA_ADMIN = 'admin';
    const AREA_FRONTEND = 'frontend';
    const AREA_INSTALL = 'install';
    const AREA_WIDGET = 'widget';

    /**
     * @return array
     */
    public static function getAreas()
    {
        return self::getConstants();
    }
}
