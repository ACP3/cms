<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Installer\Core\Controller;

use ACP3\Core\Controller\ActionInterface;
use ACP3\Core\Controller\DisplayActionTrait;
use ACP3\Modules\ACP3\Installer\Core\Controller\Context\InstallerContext;

abstract class AbstractInstallerAction implements ActionInterface
{
    use DisplayActionTrait;

    /**
     * @var \Symfony\Component\DependencyInjection\ContainerInterface
     */
    protected $container;
    /**
     * @var \ACP3\Modules\ACP3\Installer\Core\Environment\ApplicationPath
     */
    protected $appPath;
    /**
     * @var \ACP3\Core\Http\RequestInterface
     */
    protected $request;
    /**
     * @var \ACP3\Core\View
     */
    protected $view;

    public function __construct(InstallerContext $context)
    {
        $this->container = $context->getContainer();
        $this->request = $context->getRequest();
        $this->view = $context->getView();
        $this->appPath = $context->getAppPath();
    }

    /**
     * {@inheritdoc}
     */
    public function preDispatch()
    {
    }

    /**
     * @return \ACP3\Core\View
     */
    protected function getView()
    {
        return $this->view;
    }

    /**
     * {@inheritdoc}
     */
    public function get($serviceId)
    {
        return $this->container->get($serviceId);
    }

    /**
     * {@inheritdoc}
     */
    protected function applyTemplateAutomatically(): string
    {
        return $this->request->getModule()
            . '/' . \ucfirst($this->request->getArea())
            . '/' . $this->request->getController()
            . '.' . $this->request->getAction() . '.tpl';
    }
}
