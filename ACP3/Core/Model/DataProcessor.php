<?php
/**
 * Copyright (c) 2016 by the ACP3 Developers.
 * See the LICENCE file at the top-level module directory for licencing details.
 */

namespace ACP3\Core\Model;


use ACP3\Core\Model\DataProcessor\ColumnTypeStrategyFactory;

class DataProcessor
{
    /**
     * @var ColumnTypeStrategyFactory
     */
    protected $factory;

    /**
     * DataProcessor constructor.
     * @param ColumnTypeStrategyFactory $factory
     */
    public function __construct(ColumnTypeStrategyFactory $factory)
    {
        $this->factory = $factory;
    }

    /**
     * @param array $formData
     * @param array $allowedColumns
     * @return array
     */
    public function processColumnData(array $formData, array $allowedColumns)
    {
        $data = [];
        foreach ($formData as $column => $value) {
            if (array_key_exists($column, $allowedColumns)) {
                $data[$column] = $this->factory->getStrategy($allowedColumns[$column])->doEscape($value);
            } elseif (in_array($column, $allowedColumns)) {
                $data[$column] = $value;
            }
        }

        return $data;
    }
}
