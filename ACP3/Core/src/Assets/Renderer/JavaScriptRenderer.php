<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Core\Assets\Renderer;

use ACP3\Core\Assets\Renderer\Strategies\JavaScriptRendererStrategyInterface;

class JavaScriptRenderer implements RendererInterface
{
    /**
     * @var \ACP3\Core\Assets\Renderer\Strategies\JavaScriptRendererStrategyInterface
     */
    private $javaScriptRendererStrategy;

    public function __construct(JavaScriptRendererStrategyInterface $javaScriptRendererStrategy)
    {
        $this->javaScriptRendererStrategy = $javaScriptRendererStrategy;
    }

    /**
     * {@inheritDoc}
     */
    public function renderHtmlElement(string $layout = 'layout'): string
    {
        return $this->javaScriptRendererStrategy->renderHtmlElement($layout);
    }
}
