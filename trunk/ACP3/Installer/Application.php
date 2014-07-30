<?php

namespace ACP3\Installer;

use ACP3\Core;
use Doctrine\DBAL;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\Config\FileLocator;

/**
 * Class Application
 * @package ACP3\Installer
 */
class Application
{
    /**
     * @var ContainerBuilder
     */
    private static $di;
    /**
     * @var \ACP3\Core\Request
     */
    private static $uri;
    /**
     * @var \ACP3\Core\View
     */
    private static $view;

    /**
     * run() method of the installer
     */
    public static function runInstaller()
    {
        self::defineDirConstants();
        self::includeAutoLoader();
        self::initializeInstallerClasses();
        self::outputPage();
    }

    /**
     * run() method of the database updater
     */
    public static function runUpdater()
    {
        self::defineDirConstants();
        self::startupChecks();
        self::includeAutoLoader();
        self::initializeUpdaterClasses();
        self::outputPage();
    }

    /**
     * Überprüft, ob die config.php existiert
     */
    public static function startupChecks()
    {
        // Standardzeitzone festlegen
        date_default_timezone_set('UTC');

        error_reporting(E_ALL);

        if (defined('IN_UPDATER') === true) {
            // DB-Config des ACP3 laden
            $path = ACP3_DIR . 'config/config.php';
            if (is_file($path) === false || filesize($path) === 0) {
                exit('The ACP3 is not correctly installed. Please navigate to the <a href="' . ROOT_DIR . 'installation/">installation wizard</a> and follow its instructions.');
                // Wenn alles okay ist, config.php einbinden und error_reporting setzen
            } else {
                require_once $path;
            }
        }
    }

    /**
     * Einige Pfadkonstanten definieren
     */
    public static function defineDirConstants()
    {
        define('PHP_SELF', htmlentities($_SERVER['SCRIPT_NAME']));
        $php_self = dirname(PHP_SELF);
        define('ROOT_DIR', substr($php_self !== '/' ? $php_self . '/' : '/', 0, -13));
        define('INSTALLER_ROOT_DIR', $php_self !== '/' ? $php_self . '/' : '/');
        define('ACP3_DIR', ACP3_ROOT_DIR . 'ACP3/');
        define('CLASSES_DIR', ACP3_DIR . 'Core/');
        define('MODULES_DIR', ACP3_DIR . 'Modules/');
        define('LIBRARIES_DIR', ACP3_ROOT_DIR . 'libraries/');
        define('VENDOR_DIR', ACP3_ROOT_DIR . 'vendor/');
        define('UPLOADS_DIR', ACP3_ROOT_DIR . 'uploads/');
        define('CACHE_DIR', UPLOADS_DIR . 'cache/');

        define('INSTALLER_DIR', ACP3_ROOT_DIR . 'installation/');
        define('INSTALLER_MODULES_DIR', ACP3_DIR . 'Installer/Modules/');
        define('INSTALLER_CLASSES_DIR', ACP3_DIR . 'Installer/Core/');

        // Pfade zum Theme setzen
        define('DESIGN_PATH', INSTALLER_DIR . 'design/');
        define('DESIGN_PATH_INTERNAL', ACP3_ROOT_DIR . 'installation/design/');

        if (defined('IN_UPDATER') === false) {
            define('CONFIG_VERSION', '4.0-dev');
            define('CONFIG_SEO_MOD_REWRITE', false);
        }
    }

    /**
     * Klassen Autoloader inkludieren
     */
    public static function includeAutoLoader()
    {
        require VENDOR_DIR . 'autoload.php';
    }

    /**
     * Initialisieren der Klassen für den Installer
     */
    public static function initializeInstallerClasses()
    {
        \ACP3\Core\Registry::set('URI', new \ACP3\Installer\Core\URI('install', 'welcome'));

        \ACP3\Core\Registry::set('View', new Core\View());

        $params = array(
            'compile_id' => 'installer',
            'plugins_dir' => INSTALLER_CLASSES_DIR . 'View/Renderer/Smarty/',
            'template_dir' => array(DESIGN_PATH_INTERNAL, INSTALLER_MODULES_DIR)
        );
        Core\View::setRenderer('Smarty', $params);
    }

    /**
     * Initialisieren der Klassen für den Updater
     */
    public static function initializeUpdaterClasses()
    {
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

        self::$di = new ContainerBuilder();
        $loader = new YamlFileLoader(self::$di, new FileLocator(__DIR__));
        $loader->load(ACP3_DIR . 'config/services.yml');
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
        $modules = array_diff(scandir(MODULES_DIR), array('.', '..'));
        foreach ($modules as $module) {
            $path = MODULES_DIR . $module . '/config/services.yml';
            if (is_file($path)) {
                $loader->load($path);
            }
        }

        $params = array(
            'compile_id' => 'installer',
            'plugins_dir' => INSTALLER_CLASSES_DIR . 'View/Renderer/Smarty/',
            'template_dir' => array(DESIGN_PATH_INTERNAL, INSTALLER_MODULES_DIR)
        );
        Core\View::setRenderer('Smarty', $params);

        self::$di->compile();
    }

    /**
     * Gibt die Seite aus
     */
    public static function outputPage()
    {
        $view = \ACP3\Core\Registry::get('View');
        $uri = \ACP3\Core\Registry::get('URI');

        if (!empty($_POST['lang'])) {
            setcookie('ACP3_INSTALLER_LANG', $_POST['lang'], time() + 3600, '/');
            $uri->redirect($uri->mod . '/' . $uri->file);
        }

        if (!empty($_COOKIE['ACP3_INSTALLER_LANG']) && !preg_match('=/=', $_COOKIE['ACP3_INSTALLER_LANG']) &&
            is_file(ACP3_ROOT_DIR . 'installation/languages/' . $_COOKIE['ACP3_INSTALLER_LANG'] . '.xml') === true
        ) {
            define('LANG', $_COOKIE['ACP3_INSTALLER_LANG']);
        } else {
            define('LANG', \ACP3\Core\Lang::parseAcceptLanguage());
        }
        \ACP3\Core\Registry::set('Lang', new Core\Lang(LANG));

        // Einige Template Variablen setzen
        $view->assign('LANGUAGES', Core\Functions::languagesDropdown(LANG));
        $view->assign('PHP_SELF', PHP_SELF);
        $view->assign('REQUEST_URI', htmlentities($_SERVER['REQUEST_URI']));
        $view->assign('ROOT_DIR', ROOT_DIR);
        $view->assign('INSTALLER_ROOT_DIR', INSTALLER_ROOT_DIR);
        $view->assign('DESIGN_PATH', DESIGN_PATH);
        $view->assign('UA_IS_MOBILE', \ACP3\Core\Functions::isMobileBrowser());

        $lang_info = \ACP3\Core\XML::parseXmlFile(INSTALLER_DIR . 'languages/' . \ACP3\Core\Registry::get('Lang')->getLanguage() . '.xml', '/language/info');
        $view->assign('LANG_DIRECTION', isset($lang_info['direction']) ? $lang_info['direction'] : 'ltr');
        $view->assign('LANG', \ACP3\Core\Registry::get('Lang')->getLanguage2Characters());

        $module = ucfirst($uri->mod);
        $className = "\\ACP3\\Installer\\Modules\\" . $module . "\\" . $module;
        $action = 'action' . preg_replace('/(\s+)/', '', ucwords(strtolower(str_replace('_', ' ', $uri->file))));

        if (method_exists($className, $action) === true) {
            // Modul einbinden
            $mod = new $className();
            $mod->$action();
            $mod->display();
        } else {
            $uri->redirect('errors/404');
        }
    }

    /**
     * @return ContainerBuilder
     */
    public static function getServiceContainer()
    {
        return self::$di;
    }

}