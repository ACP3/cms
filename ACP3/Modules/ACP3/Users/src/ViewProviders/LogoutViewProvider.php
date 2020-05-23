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
    /**
     * @var \ACP3\Core\Http\RequestInterface
     */
    private $request;
    /**
     * @var \ACP3\Core\Environment\ApplicationPath
     */
    private $applicationPath;
    /**
     * @var \ACP3\Core\Router\RouterInterface
     */
    private $router;

    public function __construct(
        ApplicationPath $applicationPath,
        RequestInterface $request,
        RouterInterface $router
    ) {
        $this->request = $request;
        $this->applicationPath = $applicationPath;
        $this->router = $router;
    }

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
