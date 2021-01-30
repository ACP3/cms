<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Core\Controller\Context;

use ACP3\Core\Environment\ApplicationPath;
use ACP3\Core\Http\RequestInterface;
use ACP3\Core\I18n\Translator;
use ACP3\Core\Settings\SettingsInterface;
use ACP3\Core\View;
use Symfony\Component\DependencyInjection\ContainerInterface;

class WidgetContext
{
    /**
     * @var \Symfony\Component\DependencyInjection\ContainerInterface
     */
    private $container;
    /**
     * @var \ACP3\Core\I18n\Translator
     */
    private $translator;
    /**
     * @var \ACP3\Core\Http\RequestInterface
     */
    private $request;
    /**
     * @var \ACP3\Core\View
     */
    private $view;
    /**
     * @var SettingsInterface
     */
    private $config;
    /**
     * @var \ACP3\Core\Environment\ApplicationPath
     */
    private $appPath;

    public function __construct(
        ContainerInterface $container,
        Translator $translator,
        RequestInterface $request,
        View $view,
        SettingsInterface $config,
        ApplicationPath $appPath
    ) {
        $this->container = $container;
        $this->translator = $translator;
        $this->request = $request;
        $this->view = $view;
        $this->config = $config;
        $this->appPath = $appPath;
    }

    public function getContainer(): ContainerInterface
    {
        return $this->container;
    }

    public function getTranslator(): Translator
    {
        return $this->translator;
    }

    public function getRequest(): RequestInterface
    {
        return $this->request;
    }

    public function getView(): View
    {
        return $this->view;
    }

    public function getConfig(): SettingsInterface
    {
        return $this->config;
    }

    public function getAppPath(): ApplicationPath
    {
        return $this->appPath;
    }
}
