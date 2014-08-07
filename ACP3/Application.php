<?php

namespace ACP3;

use ACP3\Core\FrontController;
use ACP3\Core\Modules;
use ACP3\Core\Modules\Controller;
use ACP3\Core\Registry;
use Doctrine\DBAL;
use Monolog\ErrorHandler;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Dumper\PhpDumper;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\Config\FileLocator;

/**
 * Front Controller of the CMS
 *
 * @author Tino Goratsch
 */
class Application
{
    /**
     * @var Container
     */
    protected $container;

    /**
     * Führt alle nötigen Schritte aus, um die Seite anzuzeigen
     */
    public function run()
    {
        $this->defineDirConstants();
        $this->startupChecks();
        $this->includeAutoLoader();
        $this->setErrorHandler();
        $this->initializeClasses();
        $this->outputPage();
    }

    /**
     * Überprüft, ob die config.php existiert
     */
    public function startupChecks()
    {
        // Standardzeitzone festlegen
        date_default_timezone_set('UTC');

        // DB-Config des ACP3 laden
        $path = ACP3_DIR . 'config/config.php';
        if (is_file($path) === false || filesize($path) === 0) {
            exit('The ACP3 is not correctly installed. Please navigate to the <a href="' . ROOT_DIR . 'installation/">installation wizard</a> and follow its instructions.');
        } else {
            require_once $path;
        }
    }

    /**
     * Einige Pfadkonstanten definieren
     */
    public function defineDirConstants()
    {
        define('PHP_SELF', htmlentities($_SERVER['SCRIPT_NAME']));
        $phpSelf = dirname(PHP_SELF);
        define('ROOT_DIR', $phpSelf !== '/' ? $phpSelf . '/' : '/');
        define('HOST_NAME', 'http://' . $_SERVER['HTTP_HOST']);
        define('ROOT_DIR_ABSOLUTE', HOST_NAME . ROOT_DIR);
        define('ACP3_DIR', ACP3_ROOT_DIR . 'ACP3/');
        define('CLASSES_DIR', ACP3_DIR . 'Core/');
        define('MODULES_DIR', ACP3_DIR . 'Modules/');
        define('LIBRARIES_DIR', ACP3_ROOT_DIR . 'libraries/');
        define('VENDOR_DIR', ACP3_ROOT_DIR . 'vendor/');
        define('UPLOADS_DIR', ACP3_ROOT_DIR . 'uploads/');
        define('CACHE_DIR', UPLOADS_DIR . 'cache/');
    }

    /**
     * Klassen Autoloader inkludieren
     */
    public function includeAutoLoader()
    {
        require VENDOR_DIR . 'autoload.php';
    }

    /**
     * Set monolog as the default PHP error handler
     */
    public function setErrorHandler()
    {
        $errorLevelMap = array(
            E_ERROR => Logger::ERROR,
            E_WARNING => Logger::WARNING,
            E_PARSE => Logger::ERROR,
            E_NOTICE => Logger::NOTICE,
            E_CORE_ERROR => Logger::ERROR,
            E_CORE_WARNING => Logger::WARNING,
            E_COMPILE_ERROR => Logger::ERROR,
            E_COMPILE_WARNING => Logger::WARNING,
            E_USER_ERROR => Logger::ERROR,
            E_USER_WARNING => Logger::WARNING,
            E_USER_NOTICE => Logger::NOTICE,
            E_STRICT => Logger::WARNING,
            E_RECOVERABLE_ERROR => Logger::ERROR,
            E_DEPRECATED => Logger::WARNING,
            E_USER_DEPRECATED => Logger::WARNING,
        );

        $logger = new Logger('system', array(new StreamHandler(UPLOADS_DIR . 'logs/system.log', Logger::NOTICE)));
        ErrorHandler::register($logger, $errorLevelMap);
    }

    /**
     * Überprüfen, ob der Wartungsmodus aktiv ist
     */
    private function _checkForMaintenanceMode()
    {
        $request = $this->container->get('core.request');
        if ((bool)CONFIG_MAINTENANCE_MODE === true &&
            ($request->area !== 'admin' && strpos($request->query, 'users/login/') !== 0)
        ) {
            header('HTTP/1.0 503 Service Unavailable');

            $view = $this->container->get('core.view');
            $view->assign('PAGE_TITLE', CONFIG_SEO_TITLE);
            $view->assign('CONTENT', CONFIG_MAINTENANCE_MESSAGE);
            $view->displayTemplate('system/maintenance.tpl');
            exit;
        }
    }

    /**
     * Initialisieren der anderen Klassen
     */
    public function initializeClasses()
    {
        $file = UPLOADS_DIR . 'cache/sql/container.php';

        $config = new DBAL\Configuration();
        $connectionParams = array(
            'dbname' => CONFIG_DB_NAME,
            'user' => CONFIG_DB_USER,
            'password' => CONFIG_DB_PASSWORD,
            'host' => CONFIG_DB_HOST,
            'driver' => 'pdo_mysql',
            'charset' => 'utf8'
        );
        $db = DBAL\DriverManager::getConnection($connectionParams, $config);

        define('DB_PRE', CONFIG_DB_PRE);

        if (file_exists($file) && (!defined('DEBUG') || DEBUG === false)) {
            require_once $file;
            $this->container = new \ACP3ServiceContainer();

            $this->container->set('core.db', $db);

            // Systemeinstellungen laden
            $this->container
                ->get('system.config')
                ->getSettingsAsConstants();

            // Pfade zum Theme setzen
            define('DESIGN_PATH', ROOT_DIR . 'designs/' . CONFIG_DESIGN . '/');
            define('DESIGN_PATH_INTERNAL', ACP3_ROOT_DIR . 'designs/' . CONFIG_DESIGN . '/');
            define('DESIGN_PATH_ABSOLUTE', HOST_NAME . DESIGN_PATH);

            $this->container->get('core.view')->setRenderer('smarty');
        } else {
            $this->container = new ContainerBuilder();
            $loader = new YamlFileLoader($this->container, new FileLocator(__DIR__));
            $loader->load(ACP3_DIR . 'config/services.yml');
            $loader->load(CLASSES_DIR . 'View/Renderer/Smarty/plugins.yml');

            $this->container->set('core.db', $db);

            // Systemeinstellungen laden
            $this->container
                ->get('system.config')
                ->getSettingsAsConstants();

            // Pfade zum Theme setzen
            define('DESIGN_PATH', ROOT_DIR . 'designs/' . CONFIG_DESIGN . '/');
            define('DESIGN_PATH_INTERNAL', ACP3_ROOT_DIR . 'designs/' . CONFIG_DESIGN . '/');
            define('DESIGN_PATH_ABSOLUTE', HOST_NAME . DESIGN_PATH);

            // Try to get all available services
            /** @var Modules $modules */
            $modules = $this->container->get('core.modules');
            $activeModules = $modules->getActiveModules();
            foreach ($activeModules as $module) {
                $path = MODULES_DIR . $module['dir'] . '/config/services.yml';
                if (is_file($path)) {
                    $loader->load($path);
                }
            }

            $this->container->get('core.view')->setRenderer('smarty');

            $this->container->compile();

            $dumper = new PhpDumper($this->container);
            file_put_contents($file, $dumper->dump(array('class' => 'ACP3ServiceContainer')));
        }
    }

    /**
     * Gibt die Seite aus
     */
    public function outputPage()
    {
        $this->_checkForMaintenanceMode();

        $request = $this->container->get('core.request');
        $redirect = $this->container->get('core.redirect');

        $frontController = new FrontController($this->container);

        try {
            $frontController->dispatch();
        } catch (Core\Exceptions\ResultNotExists $e) {
            if ($e->getMessage()) {
                Core\Logger::error('404', $e);
            } else {
                Core\Logger::error('404', 'Could not find any results for request: ' . $request->query);
            }

            $redirect->temporary('errors/index/404');
        } catch (Core\Exceptions\UnauthorizedAccess $e) {
            $redirect->temporary('errors/index/401');
        } catch (Core\Exceptions\ControllerActionNotFound $e) {
            Core\Logger::error('404', 'Request: ' . $request->query);
            Core\Logger::error('404', $e);

            if (defined('DEBUG') && DEBUG === true) {
                $errorMessage = $e->getMessage();
                $this->_renderApplicationException($errorMessage);
            } else {
                $redirect->temporary('errors/index/404');
            }
        } catch (\Exception $e) {
            Core\Logger::error('exception', $e);

            if (defined('DEBUG') && DEBUG === true) {
                $errorMessage = $e->getMessage();
                $this->_renderApplicationException($errorMessage);
            } else {
                $redirect->temporary('errors/index/500');
            }
        }
    }

    /**
     * @param Container $container
     */
    public function setContainer(Container $container)
    {
        $this->container = $container;
    }

    /**
     * @return Container
     */
    public function getContainer()
    {
        return $this->container;
    }

    /**
     * Renders an exception
     * @param $errorMessage
     */
    private function _renderApplicationException($errorMessage)
    {
        $view = $this->container->get('core.view');
        $view->assign('ROOT_DIR', ROOT_DIR);
        $view->assign('PAGE_TITLE', CONFIG_SEO_TITLE);
        $view->assign('CONTENT', $errorMessage);
        $view->displayTemplate('system/maintenance.tpl');
    }
}