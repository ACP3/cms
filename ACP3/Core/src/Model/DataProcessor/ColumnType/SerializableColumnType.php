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
     */
    public function doEscape($value): string
    {
        return serialize($value);
    }

    /**
     * {@inheritdoc}
     */
    public function doUnescape($value): string
    {
        return unserialize($value);
    }

    public function getDefaultValue(): string
    {
        return serialize('');
    }
}
