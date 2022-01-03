<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Core\Controller\Context;

use ACP3\Core\Http\RequestInterface;
use ACP3\Core\I18n\Translator;
use ACP3\Core\Settings\SettingsInterface;
use ACP3\Core\View;

class WidgetContext
{
    public function __construct(
        private Translator $translator,
        private RequestInterface $request,
        private View $view,
        private SettingsInterface $config,
        private string $applicationMode
    ) {
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

    public function getApplicationMode(): string
    {
        return $this->applicationMode;
    }
}
