<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Core\Picture\Strategy;

use PHPUnit\Framework\TestCase;

class WebpPictureResizeStrategyTest extends TestCase
{
    public function testSupportedImageType(): void
    {
        self::assertSame(IMAGETYPE_WEBP, (new WebpPictureResizeStrategy())->supportedImageType());
    }
}
