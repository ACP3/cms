<?php
namespace ACP3\Core\Application;

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
    const VERSION = '4.0-dev';

    /**
     * Performs some startup checks
     */
    public function startUpChecks();

    /**
     * Initializes the dependency injection container
     */
    public function initializeClasses();

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
