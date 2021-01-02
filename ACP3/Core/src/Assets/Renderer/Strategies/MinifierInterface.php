<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Core\Assets\Renderer\Strategies;

interface MinifierInterface
{
    /**
     * Returns the URI of the minified assets.
     *
     * @deprecated To be removed with version 6.x Use MinifierInterface::renderHtmlElement instead.
     */
    public function getURI(string $layout = 'layout'): string;
}
