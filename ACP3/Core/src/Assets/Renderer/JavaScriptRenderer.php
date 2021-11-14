<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Core\Assets\Renderer;

use ACP3\Core\Assets\Renderer\Strategies\JavaScriptRendererStrategyInterface;

class JavaScriptRenderer implements RendererInterface
{
    public function __construct(private JavaScriptRendererStrategyInterface $javaScriptRendererStrategy)
    {
    }

    /**
     * {@inheritDoc}
     */
    public function renderHtmlElement(): string
    {
        return $this->javaScriptRendererStrategy->renderHtmlElement();
    }
}
