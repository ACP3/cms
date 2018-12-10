<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Core\Model\DataProcessor\ColumnType;

class BooleanColumnType implements ColumnTypeStrategyInterface
{
    /**
     * @param mixed $value
     *
     * @return bool
     */
    public function doEscape($value)
    {
        return (int) ((bool) $value);
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
