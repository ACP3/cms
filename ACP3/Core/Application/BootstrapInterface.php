<?php
namespace ACP3\Core\Application;

use Symfony\Component\HttpFoundation\Request as SymfonyRequest;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\HttpKernelInterface;

/**
 * Interface BootstrapInterface
 * @package ACP3\Core\Application
 */
interface BootstrapInterface extends HttpKernelInterface
{
    /**
     * Contains the current ACP3 version string
     */
    const VERSION = '4.0.0-rc.25';

    /**
     * Performs some startup checks
     */
    public function startUpChecks();

    /**
     * Initializes the dependency injection container
     * @param SymfonyRequest $symfonyRequest
     * @return void
     */
    public function initializeClasses(SymfonyRequest $symfonyRequest);

    /**
     * Handle the request and output the page
     * @return Response
     */
    public function outputPage();

    /**
     * @return \Symfony\Component\DependencyInjection\ContainerInterface
     */
    public function getContainer();
}
