<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Core\Assets;

use ACP3\Core\Assets\Renderer\Strategies\JavaScriptRendererStrategy;

class IncludeJs extends AbstractIncludeAsset
{
    public function __construct(
        Libraries $libraries,
        FileResolver $fileResolver,
        JavaScriptRendererStrategy $jsRendererStrategy,
    ) {
        parent::__construct($libraries, $fileResolver, $jsRendererStrategy);
    }

    protected function getResourceDirectory(): string
    {
        return 'Assets/js';
    }

    protected function getFileExtension(): string
    {
        return 'js';
    }
}
