<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Core\Assets\Renderer;

use Symfony\Component\DependencyInjection\ServiceLocator;

class JavaScriptRendererFactory
{
    public function __construct(private ServiceLocator $assetRendererStrategyServiceLocator, private string $applicationMode)
    {
    }

    public function __invoke(): JavaScriptRenderer
    {
        return new JavaScriptRenderer(
            $this->assetRendererStrategyServiceLocator->get('javascript_renderer_' . $this->applicationMode)
        );
    }
}
