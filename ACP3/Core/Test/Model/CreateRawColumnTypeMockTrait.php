<?php
/**
 * Copyright (c) 2016 by the ACP3 Developers.
 * See the LICENCE file at the top-level module directory for licencing details.
 */

namespace ACP3\Core\Test\Model;


use ACP3\Core\Model\DataProcessor\ColumnType\ColumnTypeStrategyInterface;

trait CreateRawColumnTypeMockTrait
{
    public function getRawColumnTypeInstance(\PHPUnit_Framework_TestCase $testCase)
    {
        return $testCase->getMockBuilder(ColumnTypeStrategyInterface::class)
            ->setMethods(['doEscape'])
            ->getMock();
    }
}
