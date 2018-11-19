<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Core\Application;

use ACP3\Core\Application\Exception\MaintenanceModeActiveException;
use ACP3\Core\DependencyInjection\ServiceContainerBuilder;
use ACP3\Core\Environment\ApplicationMode;
use Patchwork\Utf8;
use Symfony\Component\Config\ConfigCache;
use Symfony\Component\DependencyInjection\Dumper\PhpDumper;
use Symfony\Component\HttpFoundation\Request as SymfonyRequest;
use Symfony\Component\HttpFoundation\Response;

/**
 * Bootstraps the application.
 */
class Bootstrap extends AbstractBootstrap
{
    /**
     * {@inheritdoc}
     */
    public function handle(SymfonyRequest $request, $type = self::MASTER_REQUEST, $catch = true)
    {
        $this->setErrorHandler();
        $this->initializeClasses($request);

        return $this->outputPage();
    }

    /**
     * {@inheritdoc}
     */
    public function initializeClasses(SymfonyRequest $symfonyRequest)
    {
        Utf8\Bootup::initAll(); // Enables the portability layer and configures PHP for UTF-8
        Utf8\Bootup::filterRequestUri(); // Redirects to an UTF-8 encoded URL if it's not already the case
        Utf8\Bootup::filterRequestInputs(); // Normalizes HTTP inputs to UTF-8 NFC

        $file = $this->appPath->getCacheDir() . 'container.php';

        $this->dumpContainer($symfonyRequest, $file);

        require_once $file;

        $this->container = new \ACP3ServiceContainer();
        $this->container->set('core.environment.application_path', $this->appPath);
        $this->container->set('core.http.symfony_request', $symfonyRequest);
    }

    /**
     * @param SymfonyRequest $symfonyRequest
     * @param string         $filePath
     */
    private function dumpContainer(SymfonyRequest $symfonyRequest, $filePath)
    {
        $containerConfigCache = new ConfigCache($filePath, ($this->appMode === ApplicationMode::DEVELOPMENT));

        if (!$containerConfigCache->isFresh()) {
            $containerBuilder = ServiceContainerBuilder::create(
                $this->appPath, $symfonyRequest, $this->appMode
            );

            $dumper = new PhpDumper($containerBuilder);
            $containerConfigCache->write(
                $dumper->dump(['class' => 'ACP3ServiceContainer']),
                $containerBuilder->getResources()
            );
        }
    }

    /**
     * {@inheritdoc}
     *
     * @throws \Exception
     */
    public function outputPage()
    {
        /** @var \ACP3\Core\Http\RedirectResponse $redirect */
        $redirect = $this->container->get('core.http.redirect_response');

        try {
            /** @var \ACP3\Core\Authentication\AuthenticationInterface $authentication */
            $authentication = $this->container->get('core.authentication');
            $authentication->authenticate();

            $response = $this->container->get('core.application.controller_action_dispatcher')->dispatch();
        } catch (\ACP3\Core\Controller\Exception\ResultNotExistsException $e) {
            $response = $redirect->temporary('errors/index/not_found');
        } catch (\ACP3\Core\Authentication\Exception\UnauthorizedAccessException $e) {
            /** @var \ACP3\Core\Http\Request $request */
            $request = $this->container->get('core.http.request');
            $redirectUri = \base64_encode($request->getPathInfo());
            $response = $redirect->temporary('users/index/login/redirect_' . $redirectUri);
        } catch (\ACP3\Core\ACL\Exception\AccessForbiddenException $e) {
            $response = $redirect->temporary('errors/index/access_forbidden');
        } catch (\ACP3\Core\Controller\Exception\ControllerActionNotFoundException $e) {
            $response = $redirect->temporary('errors/index/not_found');
        } catch (MaintenanceModeActiveException $e) {
            $response = new Response($e->getMessage(), $e->getCode());
        } catch (\Exception $e) {
            $this->logger->critical($e);

            throw $e;
        }

        return $response;
    }

    /**
     * {@inheritdoc}
     */
    public function startupChecks()
    {
        \date_default_timezone_set('UTC');

        return $this->databaseConfigExists();
    }
}
