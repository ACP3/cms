<?php
/**
 * Copyright (c) 2016 by the ACP3 Developers.
 * See the LICENCE file at the top-level module directory for licencing details.
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
     * @param Date $date
     */
    public function __construct(Date $date)
    {
        $this->date = $date;
    }

    /**
     * @param string $value
     * @return string
     */
    public function doEscape($value)
    {
        return $this->date->toSQL($value);
    }
}
