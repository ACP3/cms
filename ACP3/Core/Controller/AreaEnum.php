<?php
/**
 * Copyright (c) 2016 by the ACP3 Developers.
 * See the LICENCE file at the top-level module directory for licencing details.
 */

namespace ACP3\Core\Controller;

use ACP3\Core\Enum\BaseEnum;

/**
 * Class AreaEnum
 * @package ACP3\Core\Controller
 */
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
