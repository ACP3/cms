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
    /**
     * @var \Symfony\Component\DependencyInjection\ContainerInterface
     */
    private $container;
    /**
     * @var \ACP3\Core\Http\RequestInterface
     */
    private $request;
    /**
     * @var \ACP3\Core\View
     */
    private $view;
    /**
     * @var \ACP3\Modules\ACP3\Installer\Core\Environment\ApplicationPath
     */
    private $appPath;

    public function __construct(
        ContainerInterface $container,
        RequestInterface $request,
        View $view,
        ApplicationPath $appPath
    ) {
        $this->container = $container;
        $this->request = $request;
        $this->view = $view;
        $this->appPath = $appPath;
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
