<?php

namespace ACP3\Core\Application;

use ACP3\Core\Controller\AreaEnum;
use ACP3\Core\DependencyInjection\ServiceContainerBuilder;
use ACP3\Core\Environment\ApplicationMode;
use ACP3\Core\Http\RedirectResponse;
use ACP3\Core\Http\RequestInterface;
use ACP3\Core\View;
use ACP3\Modules\ACP3\System\Installer\Schema;
use Patchwork\Utf8;
use Symfony\Component\Config\ConfigCache;
use Symfony\Component\DependencyInjection\Dumper\PhpDumper;
use Symfony\Component\HttpFoundation\Request as SymfonyRequest;
use Symfony\Component\HttpFoundation\Response;

/**
 * Bootstraps the application
 * @package ACP3\Core\Application
 */
class Bootstrap extends AbstractBootstrap
{
    /**
     * @var array
     */
    private $systemSettings = [];

    /**
     * @inheritdoc
     */
    public function handle(SymfonyRequest $request, $type = self::MASTER_REQUEST, $catch = true)
    {
        $this->setErrorHandler();
        $this->initializeClasses($request);

        return $this->outputPage();
    }

    /**
     * @inheritdoc
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
     * @param string $filePath
     */
    private function dumpContainer(SymfonyRequest $symfonyRequest, $filePath)
    {
        $containerConfigCache = new ConfigCache($filePath, ($this->appMode === ApplicationMode::DEVELOPMENT));

        if (!$containerConfigCache->isFresh()) {
            $containerBuilder = ServiceContainerBuilder::create($this->appPath, $symfonyRequest, $this->appMode);

            $dumper = new PhpDumper($containerBuilder);
            $containerConfigCache->write(
                $dumper->dump(['class' => 'ACP3ServiceContainer']),
                $containerBuilder->getResources()
            );
        }
    }

    /**
     * @inheritdoc
     */
    public function outputPage()
    {
        $this->systemSettings = $this->container->get('core.config')->getSettings(Schema::MODULE_NAME);
        $this->setThemePaths();
        $this->container->get('core.authentication')->authenticate();

        /** @var \ACP3\Core\Http\Request $request */
        $request = $this->container->get('core.http.request');

        if ($this->isMaintenanceModeEnabled($request)) {
            return $this->handleMaintenanceMode();
        }

        /** @var \ACP3\Core\Http\RedirectResponse $redirect */
        $redirect = $this->container->get('core.http.redirect_response');

        try {
            $response = $this->container->get('core.application.controller_action_dispatcher')->dispatch();

            if ($request->getArea() !== AreaEnum::AREA_WIDGET) {
                $response->headers->set('Surrogate-Control', 'content="ESI/1.0"');
            }
        } catch (\ACP3\Core\Controller\Exception\ResultNotExistsException $e) {
            $response = $redirect->temporary('errors/index/not_found');
        } catch (\ACP3\Core\Authentication\Exception\UnauthorizedAccessException $e) {
            $redirectUri = base64_encode($request->getPathInfo());
            $response = $redirect->temporary('users/index/login/redirect_' . $redirectUri);
        } catch (\ACP3\Core\ACL\Exception\AccessForbiddenException $e) {
            $response = $redirect->temporary('errors/index/access_forbidden');
        } catch (\ACP3\Core\Controller\Exception\ControllerActionNotFoundException $e) {
            $response = $this->handleException($e, $redirect, 'errors/index/not_found');
        } catch (\Exception $e) {
            $response = $this->handleException($e, $redirect, 'errors/index/server_error');
        }

        return $response;
    }

    /**
     * Sets the theme paths
     */
    private function setThemePaths()
    {
        $path = 'designs/' . $this->systemSettings['design'] . '/';

        $this->appPath
            ->setDesignPathWeb($this->appPath->getWebRoot() . $path)
            ->setDesignPathInternal(ACP3_ROOT_DIR . $path)
            ->setDesignPathAbsolute(
                $this->container->get('core.http.request')->getScheme()
                . $this->container->get('core.http.request')->getHttpHost()
                . $this->appPath->getDesignPathWeb()
            );
    }

    /**
     * Checks, whether the maintenance mode is active
     *
     * @param \ACP3\Core\Http\RequestInterface $request
     *
     * @return bool
     */
    private function isMaintenanceModeEnabled(RequestInterface $request)
    {
        return (bool)$this->systemSettings['maintenance_mode'] === true &&
        $request->getArea() !== AreaEnum::AREA_ADMIN &&
        strpos($request->getQuery(), 'users/index/login/') !== 0;
    }

    /**
     * @return Response
     */
    private function handleMaintenanceMode()
    {
        /** @var View $view */
        $view = $this->container->get('core.view');

        $view->assign([
            'PAGE_TITLE' => 'ACP3',
            'ROOT_DIR' => $this->appPath->getWebRoot(),
            'CONTENT' => $this->systemSettings['maintenance_message']
        ]);

        $response = new Response($view->fetchTemplate('system/maintenance.tpl'));
        $response->setStatusCode(Response::HTTP_SERVICE_UNAVAILABLE);

        return $response;
    }

    /**
     * @param \Exception $exception
     * @param \ACP3\Core\Http\RedirectResponse $redirect
     * @param string $route
     * @return Response
     */
    private function handleException(\Exception $exception, RedirectResponse $redirect, $route)
    {
        if ($this->appMode === ApplicationMode::DEVELOPMENT) {
            return $this->renderApplicationException($exception);
        }

        return $redirect->temporary($route);
    }

    /**
     * Renders an exception
     *
     * @param \Exception $exception
     * @return Response
     */
    private function renderApplicationException(\Exception $exception)
    {
        /** @var View $view */
        $view = $this->container->get('core.view');

        $view->assign([
            'PAGE_TITLE' => 'ACP3',
            'ROOT_DIR' => $this->appPath->getWebRoot(),
            'EXCEPTION' => $exception
        ]);

        $response = new Response($view->fetchTemplate('system/exception.tpl'));
        $response->setStatusCode(Response::HTTP_INTERNAL_SERVER_ERROR);

        return $response;
    }

    /**
     * @inheritdoc
     */
    public function startupChecks()
    {
        date_default_timezone_set('UTC');

        return $this->databaseConfigExists();
    }
}
