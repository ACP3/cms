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
     *
     * @return float
     */
    public function doEscape($value)
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

    /**
     * @return string|int
     */
    public function getDefaultValue()
    {
        return 0;
    }
}
