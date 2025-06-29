<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Core\Assets;

use ACP3\Core\Assets\Renderer\Strategies\CSSRendererStrategyInterface;

class IncludeStylesheet extends AbstractIncludeAsset
{
    public function __construct(Libraries $libraries, FileResolver $fileResolver, CSSRendererStrategyInterface $rendererStrategy)
    {
        parent::__construct($libraries, $fileResolver, $rendererStrategy);
    }

    protected function getResourceDirectory(): string
    {
        return 'Assets/css';
    }

    protected function getFileExtension(): string
    {
        return 'css';
    }
}
