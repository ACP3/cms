<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Core\Controller;

use ACP3\Core;
use ACP3\Core\Controller\Context\WidgetContext;
use ACP3\Core\Http\RequestInterface;

abstract class AbstractWidgetAction implements InvokableActionInterface
{
    use DisplayActionTrait;

    /**
     * @var \Symfony\Component\DependencyInjection\ContainerInterface
     */
    protected $container;
    /**
     * @var \ACP3\Core\I18n\Translator
     */
    protected $translator;
    /**
     * @var \ACP3\Core\Http\RequestInterface
     */
    protected $request;
    /**
     * @var \ACP3\Core\View
     */
    protected $view;
    /**
     * @var \ACP3\Core\Settings\SettingsInterface
     */
    protected $config;
    /**
     * @var \ACP3\Core\Environment\ApplicationPath
     */
    protected $appPath;

    public function __construct(WidgetContext $context)
    {
        $this->container = $context->getContainer();
        $this->translator = $context->getTranslator();
        $this->request = $context->getRequest();
        $this->view = $context->getView();
        $this->config = $context->getConfig();
        $this->appPath = $context->getAppPath();
    }

    public function preDispatch(): void
    {
    }

    /**
     * {@inheritdoc}
     */
    public function get(string $serviceId)
    {
        return $this->container->get($serviceId);
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

    protected function getView(): Core\View
    {
        return $this->view;
    }

    protected function getSettings(): Core\Settings\SettingsInterface
    {
        return $this->config;
    }

    protected function getApplicationMode(): string
    {
        return $this->container->getParameter('core.environment');
    }

    protected function getRequest(): RequestInterface
    {
        return $this->request;
    }
}
