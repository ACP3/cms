<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Core\Http;

use ACP3\Core\Settings\SettingsInterface;
use ACP3\Modules\ACP3\System\Installer\Schema;
use Symfony\Component\HttpFoundation\RequestStack;

class RequestFactory
{
    /**
     * @var SettingsInterface
     */
    protected $config;
    /**
     * @var \Symfony\Component\HttpFoundation\RequestStack
     */
    protected $requestStack;

    public function __construct(SettingsInterface $config, RequestStack $requestStack)
    {
        $this->config = $config;
        $this->requestStack = $requestStack;
    }

    /**
     * @return \ACP3\Core\Http\RequestInterface
     */
    public function create()
    {
        $request = $this->getRequest();
        $request->setHomepage($this->config->getSettings(Schema::MODULE_NAME)['homepage']);
        $request->setPathInfo();
        $request->processQuery();

        return $request;
    }

    /**
     * @return \ACP3\Core\Http\RequestInterface
     */
    protected function getRequest()
    {
        return new Request($this->requestStack);
    }
}
