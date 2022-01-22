<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Core\Model;

use Psr\Container\ContainerInterface;

class DataProcessor
{
    public function __construct(private ContainerInterface $columnTypeStrategyLocator)
    {
    }

    /**
     * @param array<string, mixed>  $columnData
     * @param array<string, string> $columnConstraints
     *
     * @return array<string, mixed>
     *
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
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

    /**
     * @param array<string, mixed>  $columnData
     * @param array<string, string> $columnConstraints
     *
     * @return array<string, mixed>
     *
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
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

    /**
     * @param array<string, mixed>  $columnData
     * @param array<string, string> $columnConstraints
     *
     * @return string[]
     *
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
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
