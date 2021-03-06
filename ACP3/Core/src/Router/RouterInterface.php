<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Core\Router;

interface RouterInterface
{
    /**
     * Generates the internal ACP3 hyperlinks.
     *
     * @param string    $path
     * @param bool      $isAbsolute
     * @param bool|null $isSecure
     *
     * @return string
     */
    public function route($path, $isAbsolute = false, $isSecure = null);
}
