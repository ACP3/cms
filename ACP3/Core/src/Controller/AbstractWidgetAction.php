<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Core\Controller;

use ACP3\Core\Controller\Context\WidgetContext;
use ACP3\Core\Environment\ApplicationPath;
use ACP3\Core\Http\RequestInterface;
use ACP3\Core\I18n\Translator;
use ACP3\Core\Settings\SettingsInterface;
use ACP3\Core\View;

abstract class AbstractWidgetAction implements InvokableActionInterface
{
    use DisplayActionTrait;

    protected Translator $translator;
    protected RequestInterface $request;
    protected View $view;
    protected SettingsInterface $config;
    protected ApplicationPath $appPath;
    private string $applicationMode;

    public function __construct(WidgetContext $context)
    {
        $this->translator = $context->getTranslator();
        $this->request = $context->getRequest();
        $this->view = $context->getView();
        $this->config = $context->getConfig();
        $this->appPath = $context->getAppPath();
        $this->applicationMode = $context->getApplicationMode();
    }

    public function preDispatch(): void
    {
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

    protected function getView(): View
    {
        return $this->view;
    }

    protected function getApplicationMode(): string
    {
        return $this->applicationMode;
    }

    protected function getRequest(): RequestInterface
    {
        return $this->request;
    }
}
