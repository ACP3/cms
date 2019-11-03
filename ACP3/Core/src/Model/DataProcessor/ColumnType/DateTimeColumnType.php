<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Core\Model\DataProcessor\ColumnType;

use ACP3\Core\Date;

class DateTimeColumnType implements ColumnTypeStrategyInterface
{
    /**
     * @var Date
     */
    protected $date;

    /**
     * DateTimeColumnType constructor.
     */
    public function __construct(Date $date)
    {
        $this->date = $date;
    }

    /**
     * @param string $value
     *
     * @return string
     */
    public function doEscape($value)
    {
        return $this->date->toSQL($value);
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
        return $this->doEscape('now');
    }
}
