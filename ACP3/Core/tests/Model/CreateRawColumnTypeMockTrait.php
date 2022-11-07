<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Core\Model;

use ACP3\Core\Model\DataProcessor\ColumnType\ColumnTypeStrategyInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

trait CreateRawColumnTypeMockTrait
{
    public function getRawColumnTypeInstance(TestCase $testCase): MockObject&ColumnTypeStrategyInterface
    {
        return $testCase->getMockBuilder(ColumnTypeStrategyInterface::class)
            ->onlyMethods(['doEscape', 'doUnescape', 'getDefaultValue'])
            ->getMock();
    }
}
