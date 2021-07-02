<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Core\Assets\Renderer;

use Symfony\Component\DependencyInjection\ServiceLocator;

class JavaScriptRendererFactory
{
    /**
     * @var \Symfony\Component\DependencyInjection\ServiceLocator
     */
    private $assetRendererStrategyServiceLocator;
    /**
     * @var string
     */
    private $applicationMode;

    public function __construct(
        ServiceLocator $assetRendererStrategyServiceLocator,
        string $applicationMode
    ) {
        $this->assetRendererStrategyServiceLocator = $assetRendererStrategyServiceLocator;
        $this->applicationMode = $applicationMode;
    }

    public function __invoke(): JavaScriptRenderer
    {
        return new JavaScriptRenderer(
            $this->assetRendererStrategyServiceLocator->get('javascript_renderer_' . $this->applicationMode)
        );
    }
}
