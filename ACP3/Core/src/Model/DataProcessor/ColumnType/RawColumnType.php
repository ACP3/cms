<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Core\Model\DataProcessor\ColumnType;

class RawColumnType implements ColumnTypeStrategyInterface
{
    public function doEscape(mixed $value)
    {
        return $value;
    }

    public function doUnescape(mixed $value): mixed
    {
        return $value;
    }

    public function getDefaultValue(): string
    {
        return '';
    }
}
