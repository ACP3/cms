<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Installer\Core\Controller\Context;

use ACP3\Core\Http\RequestInterface;
use ACP3\Core\View;
use ACP3\Modules\ACP3\Installer\Core\Environment\ApplicationPath;
use Symfony\Component\DependencyInjection\ContainerInterface;

class InstallerContext
{
    public function __construct(private ContainerInterface $container, private RequestInterface $request, private View $view, private ApplicationPath $appPath)
    {
    }

    public function getContainer(): ContainerInterface
    {
        return $this->container;
    }

    public function getRequest(): RequestInterface
    {
        return $this->request;
    }

    public function getView(): View
    {
        return $this->view;
    }

    public function getAppPath(): ApplicationPath
    {
        return $this->appPath;
    }
}
