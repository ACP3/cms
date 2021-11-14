<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Core\Assets\Renderer;

use Symfony\Component\DependencyInjection\ServiceLocator;

class CSSRendererFactory
{
    public function __construct(private ServiceLocator $assetRendererStrategyServiceLocator, private string $applicationMode)
    {
    }

    public function __invoke(): CSSRenderer
    {
        return new CSSRenderer(
            $this->assetRendererStrategyServiceLocator->get('css_renderer_' . $this->applicationMode),
            $this->assetRendererStrategyServiceLocator->get('deferrable_css_renderer_' . $this->applicationMode)
        );
    }
}
