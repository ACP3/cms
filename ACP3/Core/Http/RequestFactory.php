<?php
/**
 * Copyright (c) 2016 by the ACP3 Developers.
 * See the LICENCE file at the top-level module directory for licencing details.
 */

namespace ACP3\Core\Http;

use ACP3\Core\Config;
use ACP3\Modules\ACP3\System\Installer\Schema;
use Symfony\Component\HttpFoundation\Request as SymfonyRequest;

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
     * @var Request
     */
    protected $symfonyRequest;

    /**
     * RequestFactory constructor.
     * @param Config $config
     * @param SymfonyRequest $symfonyRequest
     */
    public function __construct(Config $config, SymfonyRequest $symfonyRequest)
    {
        $this->config = $config;
        $this->symfonyRequest = $symfonyRequest;
    }

    /**
     * @return \ACP3\Core\Http\RequestInterface
     */
    public function create()
    {
        $request = $this->getRequest();
        $request->setHomepage($this->config->getSettings(Schema::MODULE_NAME)['homepage']);
        $request->processQuery();

        return $request;
    }

    /**
     * @return \ACP3\Core\Http\Request
     */
    protected function getRequest()
    {
        return new Request($this->symfonyRequest);
    }
}
