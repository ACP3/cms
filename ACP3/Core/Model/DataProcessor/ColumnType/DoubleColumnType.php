<?php
/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENCE file at the top-level module directory for licencing details.
 */

namespace ACP3\Core\Model\DataProcessor\ColumnType;

class DoubleColumnType implements ColumnTypeStrategyInterface
{
    /**
     * @param mixed $value
     * @return float
     */
    public function doEscape($value)
    {
        return doubleval($value);
    }
}
