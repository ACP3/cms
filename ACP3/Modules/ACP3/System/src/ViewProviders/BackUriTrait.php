<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\System\ViewProviders;

use ACP3\Core\Http\RequestInterface;
use ACP3\Core\Router\RouterInterface;

trait BackUriTrait
{
    private readonly RequestInterface $request;
    private readonly RouterInterface $router;

    /**
     * @param string $fallbackUri A well-formed absolute Route (including the scheme and host) to be used as a fallback
     */
    private function getBackUri(string $fallbackUri): string
    {
        $referer = $this->request->getServer()->get('HTTP_REFERER');
        $pathInfo = $this->router->route($this->request->getPathInfo(), true);

        return $referer !== null && $referer !== $pathInfo ? $referer : $fallbackUri;
    }
}
