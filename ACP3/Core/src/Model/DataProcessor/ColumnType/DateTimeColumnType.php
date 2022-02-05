<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Core\Model\DataProcessor\ColumnType;

use ACP3\Core\Date;

class DateTimeColumnType implements ColumnTypeStrategyInterface
{
    public function __construct(private Date $date)
    {
    }

    /**
     * @param string $value
     *
     * @throws \Exception
     */
    public function doEscape($value): string
    {
        return $this->date->toSQL($value);
    }

    /**
     * {@inheritdoc}
     */
    public function doUnescape($value): string
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
