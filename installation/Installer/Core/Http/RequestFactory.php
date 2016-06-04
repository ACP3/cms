<?php
/**
 * Copyright (c) 2016 by the ACP3 Developers.
 * See the LICENCE file at the top-level module directory for licencing details.
 */

namespace ACP3\Installer\Core\Http;

use Symfony\Component\HttpFoundation\Request as SymfonyRequest;

/**
 * Class RequestFactory
 * @package ACP3\Installer\Core\Http
 */
class RequestFactory
{
    /**
     * @var SymfonyRequest
     */
    protected $symfonyRequest;

    /**
     * RequestFactory constructor.
     *
     * @param SymfonyRequest $symfonyRequest
     */
    public function __construct(SymfonyRequest $symfonyRequest)
    {
        $this->symfonyRequest = $symfonyRequest;
    }

    /**
     * @param string $homepage
     *
     * @return \ACP3\Core\Http\RequestInterface
     */
    public function create($homepage)
    {
        $request = new Request($this->symfonyRequest);
        $request->setHomepage($homepage);
        $request->processQuery();

        return $request;
    }
}
