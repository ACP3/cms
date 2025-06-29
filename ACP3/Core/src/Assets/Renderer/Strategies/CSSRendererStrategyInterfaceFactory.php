<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Core\Assets\Renderer\Strategies;

use ACP3\Core\Environment\ApplicationMode;
use Symfony\Component\DependencyInjection\ServiceLocator;

class CSSRendererStrategyInterfaceFactory
{
    /**
     * @param ServiceLocator<CSSRendererStrategyInterface> $cssRendererStrategyServiceLocator
     */
    public function __construct(private readonly ServiceLocator $cssRendererStrategyServiceLocator, readonly private ApplicationMode $applicationMode)
    {
    }

    public function __invoke(): CSSRendererStrategyInterface
    {
        return $this->cssRendererStrategyServiceLocator->get('css_renderer_' . $this->applicationMode->value);
    }
}
