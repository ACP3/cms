<?php

namespace ACP3\Core;

use ACP3\Core\Enum\Environment;
use ACP3\Core\Http\RequestInterface;
use ACP3\Core\Modules;
use ACP3\Core\Logger as ACP3Logger;
use Patchwork\Utf8;
use Symfony\Component\Config\ConfigCache;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\DependencyInjection\Dumper\PhpDumper;

/**
 * Bootstraps the application
 * @package ACP3
 */
class Application extends AbstractApplication
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
        $this->defineDirConstants();

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
     * @inheritdoc
     */
    public function defineDirConstants()
    {
        define('PHP_SELF', htmlentities($_SERVER['SCRIPT_NAME']));
        define('ROOT_DIR', substr(PHP_SELF, 0, strrpos(PHP_SELF, '/') + 1));
        define('ACP3_DIR', ACP3_ROOT_DIR . 'ACP3/');
        define('CLASSES_DIR', ACP3_DIR . 'Core/');
        define('MODULES_DIR', ACP3_DIR . 'Modules/');
        define('UPLOADS_DIR', ACP3_ROOT_DIR . 'uploads/');
        define('CACHE_DIR', ACP3_ROOT_DIR . 'cache/' . $this->environment . '/');
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
            $request->getArea() !== 'admin' &&
            strpos($request->getQuery(), 'users/index/login/') !== 0
        ) {
            header('HTTP/1.0 503 Service Unavailable');

            $view = $this->container->get('core.view');
            $view->assign('PAGE_TITLE', 'ACP3');
            $view->assign('ROOT_DIR', ROOT_DIR);
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

        $file = CACHE_DIR . 'sql/container.php';

        $this->dumpContainer($file);

        require_once $file;
        $this->container = new \ACP3ServiceContainer();
    }

    /**
     * Pfade zum Theme setzen
     */
    private function _setThemeConstants()
    {
        define('DESIGN_PATH', ROOT_DIR . 'designs/' . $this->systemSettings['design'] . '/');
        define('DESIGN_PATH_INTERNAL', ACP3_ROOT_DIR . 'designs/' . $this->systemSettings['design'] . '/');
        define('DESIGN_PATH_ABSOLUTE', $this->container->get('core.request')->getDomain() . DESIGN_PATH);
    }

    /**
     * @inheritdoc
     */
    public function outputPage()
    {
        // Load system settings
        $this->systemSettings = $this->container->get('core.config')->getSettings('system');
        $this->_setThemeConstants();
        $this->container->get('core.user')->authenticate();

        /** @var \ACP3\Core\Http\Request $request */
        $request = $this->container->get('core.request');

        if ($this->maintenanceModeIsEnabled($request)) {
            return;
        }

        /** @var \ACP3\Core\Redirect $redirect */
        $redirect = $this->container->get('core.redirect');

        try {
            (new FrontController($this->container))->dispatch();
        } catch (Exceptions\ResultNotExists $e) {
            if ($e->getMessage()) {
                ACP3Logger::error('404', $e);
            } else {
                ACP3Logger::error('404', 'Could not find any results for request: ' . $request->getQuery());
            }

            $redirect->temporary('errors/index/404')->send();
        } catch (Exceptions\UnauthorizedAccess $e) {
            $redirectUri = base64_encode($request->getOriginalQuery());
            $redirect->temporary('users/index/login/redirect_' . $redirectUri)->send();
        } catch (Exceptions\AccessForbidden $e) {
            $redirect->temporary('errors/index/403');
        } catch (Exceptions\ControllerActionNotFound $e) {
            ACP3Logger::error('404', 'Request: ' . $request->getQuery());
            ACP3Logger::error('404', $e);

            $this->handleException($e, $redirect, 'errors/index/404');
        } catch (\Exception $e) {
            ACP3Logger::error('exception', $e);

            $this->handleException($e, $redirect, 'errors/index/500');
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
     * @param string $errorMessage
     */
    private function _renderApplicationException($errorMessage)
    {
        $view = $this->container->get('core.view');
        $view->assign('ROOT_DIR', ROOT_DIR);
        $view->assign('PAGE_TITLE', 'ACP3');
        $view->assign('CONTENT', $errorMessage);
        $view->displayTemplate('system/exception.tpl');
    }

    /**
     * @param \Exception          $exception
     * @param \ACP3\Core\Redirect $redirect
     * @param string              $path
     */
    protected function handleException(\Exception $exception, Redirect $redirect, $path)
    {
        if ($this->environment === Environment::DEVELOPMENT) {
            $errorMessage = $exception->getMessage();
            $this->_renderApplicationException($errorMessage);
        } else {
            $redirect->temporary($path)->send();
        }
    }

    /**
     * @param string $file
     */
    protected function dumpContainer($file)
    {
        $containerConfigCache = new ConfigCache($file, ($this->environment === Environment::DEVELOPMENT));

        if (!$containerConfigCache->isFresh()) {
            $containerBuilder = ServiceContainerBuilder::compileContainer($this->environment);

            $dumper = new PhpDumper($containerBuilder);
            $containerConfigCache->write(
                $dumper->dump(['class' => 'ACP3ServiceContainer']),
                $containerBuilder->getResources()
            );
        }
    }
}
