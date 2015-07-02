<?php
namespace ACP3\Core;

/**
 * Interface ApplicationInterface
 * @package ACP3\Core
 */
interface ApplicationInterface
{
    /**
     * Contains the current ACP3 version string
     */
    const VERSION = '4.0-dev';

    /**
     * Executes the application bootstrapping process and outputs the requested page
     */
    function run();

    /**
     * Performs some startup checks
     */
    function startUpChecks();

    /**
     * Sets up the current environment
     */
    function defineDirConstants();

    /**
     * Initializes the dependency injection container
     */
    function initializeClasses();

    /**
     * Handle the request and output the page
     */
    function outputPage();

    /**
     * @return \Symfony\Component\DependencyInjection\ContainerInterface
     */
    function getContainer();
}