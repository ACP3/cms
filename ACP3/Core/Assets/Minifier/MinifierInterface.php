<?php
/**
 * Copyright (c) 2016 by the ACP3 Developers.
 * See the LICENCE file at the top-level module directory for licencing details.
 */

namespace ACP3\Core\Assets\Minifier;

interface MinifierInterface
{
    /**
     * Returns the URI of the minified assets
     *
     * @param string $layout
     *
     * @return string
     */
    public function getURI($layout = 'layout');
}
