<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Core\Model;

use ACP3\Core\Model\DataProcessor\ColumnType\ColumnTypeStrategyInterface;

trait CreateRawColumnTypeMockTrait
{
    public function getRawColumnTypeInstance(\PHPUnit\Framework\TestCase $testCase)
    {
        return $testCase->getMockBuilder(ColumnTypeStrategyInterface::class)
            ->setMethods(['doEscape', 'doUnescape', 'getDefaultValue'])
            ->getMock();
    }
}
