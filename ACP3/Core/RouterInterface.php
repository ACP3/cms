<?php
/**
 * Copyright (c) 2016 by the ACP3 Developers.
 * See the LICENCE file at the top-level module directory for licencing details.
 */

namespace ACP3\Core;

/**
 * Interface RouterInterface
 * @package ACP3\Core
 */
interface RouterInterface
{
    /**
     * Generates the internal ACP3 hyperlinks
     *
     * @param string $path
     * @param bool   $isAbsolute
     * @param bool   $forceSecure
     *
     * @return string
     */
    public function route($path, $isAbsolute = false, $forceSecure = false);
}
