<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Core\Enum;

trait EnumValuesTrait
{
    /**
     * @return string[]|int[]
     */
    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
