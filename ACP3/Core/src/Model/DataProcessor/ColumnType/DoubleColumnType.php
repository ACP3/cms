<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Core\Model\DataProcessor\ColumnType;

class DoubleColumnType implements ColumnTypeStrategyInterface
{
    /**
     * @param mixed $value
     */
    public function doEscape($value): float
    {
        return (float) $value;
    }

    /**
     * {@inheritdoc}
     */
    public function doUnescape($value)
    {
        return $value;
    }

    public function getDefaultValue(): int|string
    {
        return 0;
    }
}
