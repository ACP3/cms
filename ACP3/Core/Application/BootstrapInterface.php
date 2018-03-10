<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Core\Application;

use Symfony\Component\HttpFoundation\Request as SymfonyRequest;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\HttpKernelInterface;

interface BootstrapInterface extends HttpKernelInterface
{
    /**
     * Contains the current ACP3 version string.
     */
    const VERSION = '4.24.0';

    /**
     * Performs some startup checks.
     */
    public function startUpChecks();

    /**
     * Initializes the dependency injection container.
     *
     * @param SymfonyRequest $symfonyRequest
     */
    public function initializeClasses(SymfonyRequest $symfonyRequest);

    /**
     * Handle the request and output the page.
     *
     * @return Response
     */
    public function outputPage();

    /**
     * @return \Symfony\Component\DependencyInjection\ContainerInterface
     */
    public function getContainer();
}
