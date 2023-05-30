<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Core\Assets\Renderer;

use ACP3\Core\Assets\Renderer\Strategies\CSSRendererStrategyInterface;
use ACP3\Core\Environment\ApplicationMode;
use Symfony\Component\DependencyInjection\ServiceLocator;

class CSSRendererFactory
{
    /**
     * @param ServiceLocator<CSSRendererStrategyInterface> $assetRendererStrategyServiceLocator
     */
    public function __construct(private readonly ServiceLocator $assetRendererStrategyServiceLocator, readonly private ApplicationMode $applicationMode)
    {
    }

    public function __invoke(): CSSRenderer
    {
        return new CSSRenderer(
            $this->assetRendererStrategyServiceLocator->get('css_renderer_' . $this->applicationMode->value),
            $this->assetRendererStrategyServiceLocator->get('deferrable_css_renderer_' . $this->applicationMode->value)
        );
    }
}
