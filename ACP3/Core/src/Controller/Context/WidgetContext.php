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
    public function __construct(private ContainerInterface $container, private Translator $translator, private RequestInterface $request, private View $view, private SettingsInterface $config, private ApplicationPath $appPath)
    {
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
