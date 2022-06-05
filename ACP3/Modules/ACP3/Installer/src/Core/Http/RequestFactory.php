<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Installer\Core\Http;

use ACP3\Core\Http\RequestInterface;
use ACP3\Core\Settings\SettingsInterface;
use Symfony\Component\HttpFoundation\RequestStack;

class RequestFactory
{
    public function __construct(private readonly RequestStack $requestStack, private readonly SettingsInterface $settings)
    {
    }

    public function create(): RequestInterface
    {
        $request = new Request($this->requestStack);
        $request->setHomepage($this->settings->getSettings('system')['homepage']);
        $request->setPathInfo();
        $request->processQuery();

        return $request;
    }
}
