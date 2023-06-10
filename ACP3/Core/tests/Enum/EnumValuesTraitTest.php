<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Core\Enum;

use PHPUnit\Framework\TestCase;

enum TestBackendEnum: int
{
    use EnumValuesTrait;

    case foo = 0;
    case bar = 1;
}

class EnumValuesTraitTest extends TestCase
{
    public function testValues(): void
    {
        self::assertSame([0, 1], TestBackendEnum::values());
    }
}
