<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Core\Picture\Strategy;

use PHPUnit\Framework\TestCase;

class PngPictureResizeStrategyTest extends TestCase
{
    public function testSupportedImageType(): void
    {
        self::assertSame(IMAGETYPE_PNG, (new PngPictureResizeStrategy())->supportedImageType());
    }
}
