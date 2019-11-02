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
    public const VERSION = '4.46.0';

    /**
     * Checks whether the ACP3 is correctly installed.
     *
     * @return bool
     */
    public function isInstalled(): bool;

    /**
     * Initializes the dependency injection container.
     *
     * @param SymfonyRequest $symfonyRequest
     */
    public function initializeClasses(SymfonyRequest $symfonyRequest): void;

    /**
     * Handle the request and output the page.
     *
     * @return Response
     */
    public function outputPage(): Response;
}
