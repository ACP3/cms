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
     */
    public function route(string $path, bool $isAbsolute = false, bool $isSecure = null): string;
}
