<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Core\Assets\Renderer;

use ACP3\Core\Assets\Renderer\Strategies\CSSRendererStrategyInterface;

class CSSRenderer implements RendererInterface
{
    public function __construct(private CSSRendererStrategyInterface $cssRendererStrategy, private CSSRendererStrategyInterface $deferrableCssRendererStrategy)
    {
    }

    /**
     * {@inheritDoc}
     */
    public function renderHtmlElement(): string
    {
        return $this->cssRendererStrategy->renderHtmlElement() . $this->deferrableCssRendererStrategy->renderHtmlElement();
    }
}
