<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Core\Assets\Renderer;

use ACP3\Core\Assets\Renderer\Strategies\CSSRendererStrategyInterface;

class CSSRenderer implements RendererInterface
{
    /**
     * @var \ACP3\Core\Assets\Renderer\Strategies\CSSRendererStrategyInterface
     */
    private $cssRendererStrategy;
    /**
     * @var \ACP3\Core\Assets\Renderer\Strategies\CSSRendererStrategyInterface
     */
    private $deferrableCssRendererStrategy;

    public function __construct(CSSRendererStrategyInterface $cssRendererStrategy, CSSRendererStrategyInterface $deferrableCssRendererStrategy)
    {
        $this->cssRendererStrategy = $cssRendererStrategy;
        $this->deferrableCssRendererStrategy = $deferrableCssRendererStrategy;
    }

    /**
     * {@inheritDoc}
     */
    public function renderHtmlElement(string $layout = 'layout'): string
    {
        return $this->cssRendererStrategy->renderHtmlElement($layout) . $this->deferrableCssRendererStrategy->renderHtmlElement($layout);
    }
}
