<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
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
     *
     * @param ColumnTypeStrategyFactory $factory
     */
    public function __construct(ColumnTypeStrategyFactory $factory)
    {
        $this->factory = $factory;
    }

    /**
     * @param array $columnData
     * @param array $columnConstraints
     *
     * @return array
     */
    public function escape(array $columnData, array $columnConstraints)
    {
        $data = [];
        foreach ($columnData as $column => $value) {
            if (\array_key_exists($column, $columnConstraints)) {
                $data[$column] = $this->factory->getStrategy($columnConstraints[$column])->doEscape($value);
            }
        }

        foreach ($this->findMissingColumns($columnData, $columnConstraints) as $columnName) {
            $data[$columnName] = $this->factory->getStrategy($columnConstraints[$columnName])->getDefaultValue();
        }

        return $data;
    }

    /**
     * @param array $columnData
     * @param array $columnConstraints
     *
     * @return array
     */
    public function unescape(array $columnData, array $columnConstraints)
    {
        $data = [];
        foreach ($columnData as $column => $value) {
            if (\array_key_exists($column, $columnConstraints)) {
                $data[$column] = $this->factory->getStrategy($columnConstraints[$column])->doUnescape($value);
            }
        }

        foreach ($this->findMissingColumns($columnData, $columnConstraints) as $columnName) {
            $data[$columnName] = $this->factory->getStrategy($columnConstraints[$columnName])->getDefaultValue();
        }

        return $data;
    }

    /**
     * @param array $columnData
     * @param array $columnConstraints
     *
     * @return array
     */
    private function findMissingColumns(array $columnData, array $columnConstraints): array
    {
        $missingColumns = \array_diff(
            \array_keys($columnConstraints),
            \array_intersect(
                \array_keys($columnData),
                \array_keys($columnConstraints)
            )
        );

        return $missingColumns;
    }
}
