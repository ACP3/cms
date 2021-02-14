<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Core\DataGrid;

class QueryOption
{
    /**
     * @var string
     */
    private $tableAlias;
    /**
     * @var string
     */
    private $columnName;
    /**
     * @var string
     */
    private $value;
    /**
     * @var string
     */
    private $operator;

    public function __construct(
        string $columnName,
        string $value,
        string $tableAlias = 'main',
        string $operator = '='
    ) {
        $this->tableAlias = $tableAlias;
        $this->value = $value;
        $this->columnName = $columnName;
        $this->operator = $operator;
    }

    public function getTableAlias(): string
    {
        return $this->tableAlias;
    }

    public function getColumnName(): string
    {
        return $this->columnName;
    }

    public function getValue(): string
    {
        return $this->value;
    }

    public function getOperator(): string
    {
        return $this->operator;
    }
}
