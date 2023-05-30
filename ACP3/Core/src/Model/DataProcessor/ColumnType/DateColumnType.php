<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Core\Model\DataProcessor\ColumnType;

use ACP3\Core\Date;

class DateColumnType implements ColumnTypeStrategyInterface
{
    public function __construct(private readonly Date $date)
    {
    }

    public function doEscape(mixed $value): string
    {
        return $this->date->format($value, Date::DEFAULT_DATE_FORMAT_SHORT);
    }

    public function doUnescape(mixed $value): mixed
    {
        return $value;
    }

    public function getDefaultValue(): int|string
    {
        return $this->doEscape('now');
    }
}
