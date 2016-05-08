<?php

namespace ACP3\Core\Application;

use ACP3\Core\Controller\AreaEnum;
use ACP3\Core\Environment\ApplicationMode;
use ACP3\Core\Http\RedirectResponse;
use ACP3\Core\Http\RequestInterface;
use ACP3\Core\Modules;
use ACP3\Core\ServiceContainerBuilder;
use Patchwork\Utf8;
use Symfony\Component\Config\ConfigCache;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\DependencyInjection\Dumper\PhpDumper;

/**
 * Bootstraps the application
 * @package ACP3\Core\Application
 */
class Bootstrap extends AbstractBootstrap
{
    /**
     * @var array
     */
    protected $systemSettings = [];

    /**
     * @inheritdoc
     */
    public function run()
    {
        if ($this->startupChecks()) {
            $this->setErrorHandler();
            $this->initializeClasses();
            $this->outputPage();
        }
    }

    /**
     * @inheritdoc
     */
    public function startupChecks()
    {
        // Standardzeitzone festlegen
        date_default_timezone_set('UTC');

        return $this->databaseConfigExists();
    }

    /**
     * Checks, whether the maintenance mode is active
     *
     * @param \ACP3\Core\Http\RequestInterface $request
     *
     * @return bool
     */
    private function maintenanceModeIsEnabled(RequestInterface $request)
    {
        if ((bool)$this->systemSettings['maintenance_mode'] === true &&
            $request->getArea() !== AreaEnum::AREA_ADMIN &&
            strpos($request->getQuery(), 'users/index/login/') !== 0
        ) {
            header('HTTP/1.0 503 Service Unavailable');

            $view = $this->container->get('core.view');
            $view->assign('PAGE_TITLE', 'ACP3');
            $view->assign('ROOT_DIR', $this->appPath->getWebRoot());
            $view->assign('CONTENT', $this->systemSettings['maintenance_message']);
            $view->displayTemplate('system/maintenance.tpl');

            return true;
        }

        return false;
    }

    /**
     * @inheritdoc
     */
    public function initializeClasses()
    {
        Utf8\Bootup::initAll(); // Enables the portability layer and configures PHP for UTF-8
        Utf8\Bootup::filterRequestUri(); // Redirects to an UTF-8 encoded URL if it's not already the case
        Utf8\Bootup::filterRequestInputs(); // Normalizes HTTP inputs to UTF-8 NFC

        $file = $this->appPath->getCacheDir() . 'sql/container.php';

        $this->dumpContainer($file);

        require_once $file;

        $this->container = new \ACP3ServiceContainer();
        $this->container->set('core.environment.application_path', $this->appPath);
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
            ->setDesignPathAbsolute($this->container->get('core.http.request')->getDomain() . $this->appPath->getDesignPathWeb());
    }

    /**
     * @inheritdoc
     */
    public function outputPage()
    {
        // Load system settings
        $this->systemSettings = $this->container->get('core.config')->getSettings('system');
        $this->setThemePaths();
        $this->container->get('core.user')->authenticate();

        /** @var \ACP3\Core\Http\Request $request */
        $request = $this->container->get('core.http.request');

        if ($this->maintenanceModeIsEnabled($request)) {
            return;
        }

        /** @var \ACP3\Core\Http\RedirectResponse $redirect */
        $redirect = $this->container->get('core.http.redirect_response');

        try {
            $this->container->get('core.application.controller_resolver')->dispatch();
        } catch (\ACP3\Core\Controller\Exception\ResultNotExistsException $e) {
            $redirect->temporary('errors/index/not_found')->send();
        } catch (\ACP3\Core\Authentication\Exception\UnauthorizedAccessException $e) {
            $redirectUri = base64_encode($request->getOriginalQuery());
            $redirect->temporary('users/index/login/redirect_' . $redirectUri)->send();
        } catch (\ACP3\Core\ACL\Exception\AccessForbiddenException $e) {
            $redirect->temporary('errors/index/access_forbidden');
        } catch (\ACP3\Core\Controller\Exception\ControllerActionNotFoundException $e) {
            $this->handleException($e, $redirect, 'errors/index/not_found');
        } catch (\Exception $e) {
            $this->handleException($e, $redirect, 'errors/index/server_error');
        }
    }

    /**
     * @param \Symfony\Component\DependencyInjection\ContainerInterface $container
     */
    public function setContainer(ContainerInterface $container)
    {
        $this->container = $container;
    }

    /**
     * Renders an exception
     *
     * @param \Exception $exception
     */
    private function renderApplicationException(\Exception $exception)
    {
        $view = $this->container->get('core.view');
        $view->assign('ROOT_DIR', $this->appPath->getWebRoot());
        $view->assign('PAGE_TITLE', 'ACP3');
        $view->assign('EXCEPTION', $exception);
        $view->displayTemplate('system/exception.tpl');
    }

    /**
     * @param \Exception                       $exception
     * @param \ACP3\Core\Http\RedirectResponse $redirect
     * @param string                           $path
     */
    protected function handleException(\Exception $exception, RedirectResponse $redirect, $path)
    {
        if ($this->appMode === ApplicationMode::DEVELOPMENT) {
            $this->renderApplicationException($exception);
        } else {
            $redirect->temporary($path)->send();
        }
    }

    /**
     * @param string $file
     */
    protected function dumpContainer($file)
    {
        $containerConfigCache = new ConfigCache($file, ($this->appMode === ApplicationMode::DEVELOPMENT));

        if (!$containerConfigCache->isFresh()) {
            $containerBuilder = ServiceContainerBuilder::compileContainer($this->appMode, $this->appPath);

            $dumper = new PhpDumper($containerBuilder);
            $containerConfigCache->write(
                $dumper->dump(['class' => 'ACP3ServiceContainer']),
                $containerBuilder->getResources()
            );
        }
    }
}
