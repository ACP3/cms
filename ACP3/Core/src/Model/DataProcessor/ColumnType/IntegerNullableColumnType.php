<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Core\Model\DataProcessor\ColumnType;

class IntegerNullableColumnType implements ColumnTypeStrategyInterface
{
    public function doEscape(mixed $value): ?int
    {
        if ($value !== null) {
            $value = (int) $value;
        }

        return $value;
    }

    public function doUnescape(mixed $value): mixed
    {
        return $value;
    }

    /**
     * @return null
     */
    public function getDefaultValue()
    {
        return null;
    }
}
