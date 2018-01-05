<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Installer\Core\Http;

use Symfony\Component\HttpFoundation\Request as SymfonyRequest;

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
