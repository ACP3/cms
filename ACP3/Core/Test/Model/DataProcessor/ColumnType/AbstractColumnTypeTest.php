<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Core\Test\Model\DataProcessor\ColumnType;

use ACP3\Core\Model\DataProcessor\ColumnType\ColumnTypeStrategyInterface;

abstract class AbstractColumnTypeTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var ColumnTypeStrategyInterface
     */
    protected $columnType;

    protected function setUp()
    {
        $this->instantiateClassToTest();
    }

    abstract protected function instantiateClassToTest();

    abstract public function testDoEscape();
}
