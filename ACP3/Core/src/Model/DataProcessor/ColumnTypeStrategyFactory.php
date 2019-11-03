<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Core\Model\DataProcessor;

use ACP3\Core\Model\DataProcessor\ColumnType\ColumnTypeStrategyInterface;

class ColumnTypeStrategyFactory
{
    /**
     * @var ColumnTypeStrategyInterface[]
     */
    protected $columnTypes = [];

    public function registerColumnType(ColumnTypeStrategyInterface $columnTypeStrategy, string $columnTypeName)
    {
        $this->columnTypes[$columnTypeName] = $columnTypeStrategy;

        return $this;
    }

    public function getStrategy(string $columnType): ColumnTypeStrategyInterface
    {
        if (!isset($this->columnTypes[$columnType])) {
            throw new \InvalidArgumentException('Invalid column type constraint given: ' . $columnType);
        }

        return $this->columnTypes[$columnType];
    }
}
