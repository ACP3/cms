<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Core\Model\DataProcessor\ColumnType;

class SerializableColumnType implements ColumnTypeStrategyInterface
{
    /**
     * @param mixed $value
     *
     * @return string
     */
    public function doEscape($value)
    {
        return \serialize($value);
    }

    /**
     * @return string|int
     */
    public function getDefaultValue()
    {
        return \serialize('');
    }
}
