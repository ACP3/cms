<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Core\Model\DataProcessor\ColumnType;

class IntegerColumnType implements ColumnTypeStrategyInterface
{
    public function doEscape(mixed $value): int
    {
        return (int) $value;
    }

    public function doUnescape(mixed $value): mixed
    {
        return $value;
    }

    public function getDefaultValue(): int
    {
        return 0;
    }
}
