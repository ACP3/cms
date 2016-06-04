<?php
/**
 * Copyright (c) 2016 by the ACP3 Developers.
 * See the LICENCE file at the top-level module directory for licencing details.
 */

namespace ACP3\Installer\Core\Http;

use ACP3\Core\Environment\ApplicationPath;
use Symfony\Component\HttpFoundation\Request as SymfonyRequest;

/**
 * Class RequestFactory
 * @package ACP3\Installer\Core\Http
 */
class RequestFactory
{
    /**
     * @var \ACP3\Core\Environment\ApplicationPath
     */
    protected $appPath;
    /**
     * @var SymfonyRequest
     */
    protected $symfonyRequest;

    /**
     * RequestFactory constructor.
     *
     * @param \ACP3\Core\Environment\ApplicationPath $appPath
     * @param SymfonyRequest $symfonyRequest
     */
    public function __construct(ApplicationPath $appPath, SymfonyRequest $symfonyRequest)
    {
        $this->appPath = $appPath;
        $this->symfonyRequest = $symfonyRequest;
    }

    /**
     * @param string $homepage
     *
     * @return \ACP3\Core\Http\RequestInterface
     */
    public function create($homepage)
    {
        $request = new Request($this->symfonyRequest, $this->appPath);
        $request->setHomepage($homepage);
        $request->processQuery();

        return $request;
    }
}
