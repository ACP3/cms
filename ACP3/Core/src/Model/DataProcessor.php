<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Core\Model;

use Psr\Container\ContainerInterface;

class DataProcessor
{
    /**
     * @var \Psr\Container\ContainerInterface
     */
    private $columnTypeStrategyLocator;

    public function __construct(ContainerInterface $columnTypeStrategyLocator)
    {
        $this->columnTypeStrategyLocator = $columnTypeStrategyLocator;
    }

    public function escape(array $columnData, array $columnConstraints): array
    {
        $data = [];
        foreach ($columnData as $column => $value) {
            if (\array_key_exists($column, $columnConstraints)) {
                $data[$column] = $this->columnTypeStrategyLocator->get($columnConstraints[$column])->doEscape($value);
            }
        }

        foreach ($this->findMissingColumns($columnData, $columnConstraints) as $columnName) {
            $data[$columnName] = $this->columnTypeStrategyLocator->get($columnConstraints[$columnName])->getDefaultValue();
        }

        return $data;
    }

    public function unescape(array $columnData, array $columnConstraints): array
    {
        $data = [];
        foreach ($columnData as $column => $value) {
            if (\array_key_exists($column, $columnConstraints)) {
                $data[$column] = $this->columnTypeStrategyLocator->get($columnConstraints[$column])->doUnescape($value);
            }
        }

        foreach ($this->findMissingColumns($columnData, $columnConstraints) as $columnName) {
            $data[$columnName] = $this->columnTypeStrategyLocator->get($columnConstraints[$columnName])->getDefaultValue();
        }

        return $data;
    }

    private function findMissingColumns(array $columnData, array $columnConstraints): array
    {
        return array_diff(
            array_keys($columnConstraints),
            array_intersect(
                array_keys($columnData),
                array_keys($columnConstraints)
            )
        );
    }
}
