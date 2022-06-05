<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Core\DataGrid;

class QueryOption
{
    public function __construct(private readonly string $columnName, private readonly string $value, private readonly string $tableAlias = 'main', private readonly string $operator = '=')
    {
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
