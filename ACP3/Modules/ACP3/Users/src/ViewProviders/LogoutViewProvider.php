<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Users\ViewProviders;

use ACP3\Core\Environment\ApplicationPath;
use ACP3\Core\Http\RequestInterface;
use ACP3\Core\Router\RouterInterface;

class LogoutViewProvider
{
    public function __construct(private readonly ApplicationPath $applicationPath, private readonly RequestInterface $request, private readonly RouterInterface $router)
    {
    }

    /**
     * @return array<string, mixed>
     */
    public function __invoke(): array
    {
        $redirectUrl = $this->applicationPath->getWebRoot();
        $referer = $this->request->getSymfonyRequest()->headers->get('referer');
        if ($referer !== $this->router->route($this->request->getPathInfo())) {
            $redirectUrl = $referer;
        }

        return [
            'url_homepage' => $this->applicationPath->getWebRoot(),
            'url_previous_page' => $redirectUrl,
        ];
    }
}
