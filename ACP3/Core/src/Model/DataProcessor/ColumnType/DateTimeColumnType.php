<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Core\Model\DataProcessor\ColumnType;

use ACP3\Core\Date;

class DateTimeColumnType implements ColumnTypeStrategyInterface
{
    public function __construct(private readonly Date $date)
    {
    }

    /**
     * @throws \Exception
     */
    public function doEscape(mixed $value): string
    {
        return $this->date->toSQL($value);
    }

    public function doUnescape(mixed $value): mixed
    {
        return $value;
    }

    /**
     * @throws \Exception
     */
    public function getDefaultValue(): int|string
    {
        return $this->doEscape('now');
    }
}
