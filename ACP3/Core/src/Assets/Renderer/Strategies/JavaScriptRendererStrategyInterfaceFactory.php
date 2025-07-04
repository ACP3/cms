<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Core\Assets\Renderer\Strategies;

use ACP3\Core\Environment\ApplicationMode;
use Symfony\Component\DependencyInjection\ServiceLocator;

class JavaScriptRendererStrategyInterfaceFactory
{
    /**
     * @param ServiceLocator<JavaScriptRendererStrategyInterface> $javaScriptRendererStrategyServiceLocator
     */
    public function __construct(private readonly ServiceLocator $javaScriptRendererStrategyServiceLocator, readonly private ApplicationMode $applicationMode)
    {
    }

    public function __invoke(): JavaScriptRendererStrategyInterface
    {
        return $this->javaScriptRendererStrategyServiceLocator->get('js_renderer_' . $this->applicationMode->value);
    }
}
