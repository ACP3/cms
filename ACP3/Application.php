<?php

namespace ACP3;

use ACP3\Core\Modules;
use ACP3\Core\Modules\Controller;
use Doctrine\DBAL;
use Monolog\ErrorHandler;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;
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
     * @var ContainerBuilder
     */
    private static $di;
    /**
     * @var \ACP3\Core\URI
     */
    private static $uri;
    /**
     * @var \ACP3\Core\View
     */
    private static $view;

    /**
     * Führt alle nötigen Schritte aus, um die Seite anzuzeigen
     */
    public static function run()
    {
        self::defineDirConstants();
        self::startupChecks();
        self::includeAutoLoader();
        self::setErrorHandler();
        self::initializeClasses();
        self::outputPage();
    }

    /**
     * Überprüft, ob die config.php existiert
     */
    public static function startupChecks()
    {
        // Standardzeitzone festlegen
        date_default_timezone_set('UTC');

        // DB-Config des ACP3 laden
        $path = ACP3_DIR . 'config.php';
        if (is_file($path) === false || filesize($path) === 0) {
            exit('The ACP3 is not correctly installed. Please navigate to the <a href="' . ROOT_DIR . 'installation/">installation wizard</a> and follow its instructions.');
        } else {
            require_once ACP3_DIR . 'config.php';
        }
    }

    /**
     * Einige Pfadkonstanten definieren
     */
    public static function defineDirConstants()
    {
        define('PHP_SELF', htmlentities($_SERVER['SCRIPT_NAME']));
        $php_self = dirname(PHP_SELF);
        define('ROOT_DIR', $php_self !== '/' ? $php_self . '/' : '/');
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
    public static function includeAutoLoader()
    {
        require VENDOR_DIR . 'autoload.php';
    }

    /**
     * Set monolog as the default PHP error handler
     */
    public static function setErrorHandler()
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
    private static function _checkForMaintenanceMode()
    {
        if ((bool)CONFIG_MAINTENANCE_MODE === true &&
            (self::$uri->area !== 'admin' && strpos(self::$uri->query, 'users/login/') !== 0)
        ) {
            header('HTTP/1.0 503 Service Unavailable');

            self::$view->assign('PAGE_TITLE', CONFIG_SEO_TITLE);
            self::$view->assign('CONTENT', CONFIG_MAINTENANCE_MESSAGE);
            self::$view->displayTemplate('system/maintenance.tpl');
            exit;
        }
    }

    /**
     * Initialisieren der anderen Klassen
     */
    public static function initializeClasses()
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

        if (file_exists($file)) {
            require_once $file;
            self::$di = new \ACP3ServiceContainer();

            self::$di->set('core.db', $db);

            // Systemeinstellungen laden
            self::$di
                ->get('system.config')
                ->getSettingsAsConstants();

            // Pfade zum Theme setzen
            define('DESIGN_PATH', ROOT_DIR . 'designs/' . CONFIG_DESIGN . '/');
            define('DESIGN_PATH_INTERNAL', ACP3_ROOT_DIR . 'designs/' . CONFIG_DESIGN . '/');
            define('DESIGN_PATH_ABSOLUTE', HOST_NAME . DESIGN_PATH);

            Core\View::factory('Smarty');
        } else {
            self::$di = new ContainerBuilder();
            $loader = new YamlFileLoader(self::$di, new FileLocator(__DIR__));
            $loader->load(CLASSES_DIR . 'services.yml');
            $loader->load(CLASSES_DIR . 'View/Renderer/Smarty/plugins.yml');

            self::$di->set('core.db', $db);

            // Systemeinstellungen laden
            self::$di
                ->get('system.config')
                ->getSettingsAsConstants();

            // Pfade zum Theme setzen
            define('DESIGN_PATH', ROOT_DIR . 'designs/' . CONFIG_DESIGN . '/');
            define('DESIGN_PATH_INTERNAL', ACP3_ROOT_DIR . 'designs/' . CONFIG_DESIGN . '/');
            define('DESIGN_PATH_ABSOLUTE', HOST_NAME . DESIGN_PATH);

            // Try to get all available services
            /** @var Modules $modules */
            $modules = self::$di->get('core.modules');
            $activeModules = $modules->getActiveModules();
            foreach ($activeModules as $module) {
                if ($module['has_services'] === true) {
                    $path = MODULES_DIR . $module['dir'] . '/services.yml';
                    if (is_file($path)) {
                        $loader->load($path);
                    }
                }
            }

            Core\View::factory('Smarty');

            self::$di->compile();

            $dumper = new PhpDumper(self::$di);
            file_put_contents($file, $dumper->dump(array('class' => 'ACP3ServiceContainer')));
        }
    }

    /**
     * Gibt die Seite aus
     */
    public static function outputPage()
    {
        self::_checkForMaintenanceMode();

        $auth = self::$di->get('core.auth');
        $uri = self::$di->get('core.uri');

        // Aktuelle Datensatzposition bestimmen
        define('POS', self::$di->get('core.validate')->isNumber($uri->page) && $uri->page >= 1 ? (int)($uri->page - 1) * $auth->entries : 0);

        try {
            $module = ucfirst($uri->mod);

            if ($uri->area !== 'frontend') {
                $className = "\\ACP3\\Modules\\" . $module . "\\Controller\\" . ucfirst($uri->area) . "\\" . ucfirst($uri->controller);
            } else {
                $className = "\\ACP3\\Modules\\" . $module . "\\Controller\\" . ucfirst($uri->controller);
            }

            self::dispatch($className, $uri->file);
        } catch (Core\Exceptions\ResultNotExists $e) {
            if ($e->getMessage()) {
                Core\Logger::error('404', $e);
            } else {
                Core\Logger::error('404', 'Could not find any results for request: ' . $uri->query);
            }

            $uri->redirect('errors/index/404');
        } catch (Core\Exceptions\UnauthorizedAccess $e) {
            $uri->redirect('errors/index/401');
        } catch (Core\Exceptions\ControllerActionNotFound $e) {
            Core\Logger::error('404', 'Request: ' . $uri->query);
            Core\Logger::error('404', $e);

            if (defined('DEBUG') && DEBUG === true) {
                $errorMessage = $e->getMessage();
                self::_renderApplicationException($errorMessage);
            } else {
                $uri->redirect('errors/index/404');
            }
        } catch (\Exception $e) {
            Core\Logger::error('exception', $e);

            if (defined('DEBUG') && DEBUG === true) {
                $errorMessage = $e->getMessage();
                self::_renderApplicationException($errorMessage);
            } else {
                $uri->redirect('errors/index/500');
            }
        }
    }

    /**
     * @param $className
     * @param $action
     * @throws Core\Exceptions\ControllerActionNotFound
     */
    public static function dispatch($className, $action)
    {
        if (class_exists($className)) {
            /** @var Controller $controller */
            $controller = new $className(
                self::$di->get('core.auth'),
                self::$di->get('core.breadcrumb'),
                self::$di->get('core.date'),
                self::$di->get('core.db'),
                self::$di->get('core.lang'),
                self::$di->get('core.session'),
                self::$di->get('core.uri'),
                self::$di->get('core.view'),
                self::$di->get('core.seo'),
                self::$di->get('core.modules'),
                self::$di->get('core.acl')
            );

            $action = 'action' . str_replace('_', '', $action);

            if (method_exists($controller, $action) === true) {
                $controller->setContainer(self::$di);
                $controller->preDispatch();
                $controller->$action();
                $controller->display();
            } else {
                throw new Core\Exceptions\ControllerActionNotFound('Controller action ' . $className . '::' . $action . '() was not found!');
            }
        } else {
            throw new Core\Exceptions\ControllerActionNotFound('Class ' . $className . '() was not found!');
        }
    }

    /**
     * @return ContainerBuilder
     */
    public static function getServiceContainer()
    {
        return self::$di;
    }

    /**
     * Renders an exception
     * @param $errorMessage
     */
    private static function _renderApplicationException($errorMessage)
    {
        $view = self::$di->get('core.view');
        $view->assign('PAGE_TITLE', CONFIG_SEO_TITLE);
        $view->assign('CONTENT', $errorMessage);
        $view->displayTemplate('system/maintenance.tpl');
    }
}