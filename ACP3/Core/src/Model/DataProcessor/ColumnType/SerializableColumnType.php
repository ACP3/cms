<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Core\Model\DataProcessor\ColumnType;

class SerializableColumnType implements ColumnTypeStrategyInterface
{
    public function doEscape(mixed $value): string
    {
        return serialize($value);
    }

    public function doUnescape(mixed $value): mixed
    {
        return unserialize($value);
    }

    public function getDefaultValue(): string
    {
        return serialize('');
    }
}
