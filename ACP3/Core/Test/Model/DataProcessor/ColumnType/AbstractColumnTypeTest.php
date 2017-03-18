<?php
/**
 * Copyright (c) 2016 by the ACP3 Developers.
 * See the LICENCE file at the top-level module directory for licencing details.
 */

namespace ACP3\Core\Test\Model\DataProcessor\ColumnType;

use ACP3\Core\Model\DataProcessor\ColumnType\ColumnTypeStrategyInterface;

abstract class AbstractColumnTypeTest extends \PHPUnit_Framework_TestCase
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
