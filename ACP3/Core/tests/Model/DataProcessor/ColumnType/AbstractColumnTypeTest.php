<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Core\Model\DataProcessor\ColumnType;

abstract class AbstractColumnTypeTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var ColumnTypeStrategyInterface
     */
    protected $columnType;

    protected function setup(): void
    {
        $this->instantiateClassToTest();
    }

    abstract protected function instantiateClassToTest(): void;

    abstract public function testDoEscape(): void;
}
