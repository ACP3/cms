<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Core\Assets\Renderer;

interface RendererInterface
{
    /**
     * Returns the completely rendered HTML element of the to be rendered assets.
     */
    public function renderHtmlElement(string $layout = 'layout'): string;
}
