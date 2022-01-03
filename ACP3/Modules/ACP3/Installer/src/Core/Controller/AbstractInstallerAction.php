<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Installer\Core\Controller;

use ACP3\Core\Controller\DisplayActionTrait;
use ACP3\Core\Controller\InvokableActionInterface;
use ACP3\Core\Http\RequestInterface;
use ACP3\Core\View;
use ACP3\Modules\ACP3\Installer\Core\Controller\Context\InstallerContext;

abstract class AbstractInstallerAction implements InvokableActionInterface
{
    use DisplayActionTrait;

    protected RequestInterface $request;
    protected View $view;

    public function __construct(InstallerContext $context)
    {
        $this->request = $context->getRequest();
        $this->view = $context->getView();
    }

    /**
     * {@inheritdoc}
     */
    public function preDispatch(): void
    {
    }

    protected function getView(): View
    {
        return $this->view;
    }

    /**
     * {@inheritdoc}
     */
    protected function applyTemplateAutomatically(): string
    {
        return $this->request->getModule()
            . '/' . ucfirst($this->request->getArea())
            . '/' . $this->request->getController()
            . '.' . $this->request->getAction() . '.tpl';
    }
}
