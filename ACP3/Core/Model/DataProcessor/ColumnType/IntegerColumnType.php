<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Core\Model\DataProcessor\ColumnType;

class IntegerColumnType implements ColumnTypeStrategyInterface
{
    /**
     * @param mixed $value
     *
     * @return int
     */
    public function doEscape($value)
    {
        return (int) $value;
    }
}
