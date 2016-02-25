<?php
/**
 * Copyright (c) 2016 by the ACP3 Developers.
 * See the LICENCE file at the top-level module directory for licencing details.
 */

namespace ACP3\Core\Http;

use ACP3\Core\Config;
use ACP3\Core\Environment\ApplicationPath;

/**
 * Class RequestFactory
 * @package ACP3\Core\Http
 */
class RequestFactory
{
    /**
     * @var \ACP3\Core\Config
     */
    protected $config;
    /**
     * @var \ACP3\Core\Environment\ApplicationPath
     */
    protected $appPath;

    /**
     * RequestFactory constructor.
     *
     * @param \ACP3\Core\Config                      $config
     * @param \ACP3\Core\Environment\ApplicationPath $appPath
     */
    public function __construct(Config $config, ApplicationPath $appPath)
    {
        $this->config = $config;
        $this->appPath = $appPath;
    }

    /**
     * @return \ACP3\Core\Http\RequestInterface
     */
    public function create()
    {
        $request = $this->getRequest();
        $request->setHomepage($this->config->getSettings('system')['homepage']);
        $request->processQuery();

        return $request;
    }

    /**
     * @return \ACP3\Core\Http\Request
     */
    protected function getRequest()
    {
        return new Request($this->appPath);
    }
}
