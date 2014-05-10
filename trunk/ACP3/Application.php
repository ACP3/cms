<?php

namespace ACP3;

use ACP3\Core\Modules\Controller;
use Doctrine\DBAL;
use Monolog\ErrorHandler;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;

/**
 * Front Controller of the CMS
 *
 * @author Tino Goratsch
 */
class Application
{
    /**
     * @var \ACP3\Core\Auth
     */
    private static $auth;
    /**
     * @var \ACP3\Core\Breadcrumb
     */
    private static $breadcrumb;
    /**
     * @var \ACP3\Core\Date
     */
    private static $date;
    /**
     * @var DBAL\Connection
     */
    private static $db;
    /**
     * @var \ACP3\Core\Lang
     */
    private static $lang;
    /**
     * @var \ACP3\Core\SEO
     */
    private static $seo;
    /**
     * @var \ACP3\Core\Session
     */
    private static $session;
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
            E_ERROR,
            E_PARSE,
            E_CORE_ERROR,
            E_COMPILE_ERROR,
            E_USER_ERROR,
            E_WARNING,
            E_COMPILE_WARNING,
            E_DEPRECATED,
            E_NOTICE,
            E_USER_NOTICE
        );

        $logger = new Logger('system', array(new StreamHandler(UPLOADS_DIR . 'logs/system.log', Logger::NOTICE)));
        ErrorHandler::register($logger, $errorLevelMap);
    }

    /**
     * Überprüfen, ob der Wartungsmodus aktiv ist
     */
    public static function checkForMaintenanceMode()
    {
        if ((bool)CONFIG_MAINTENANCE_MODE === true &&
            (defined('IN_ADM') === false && strpos(self::$uri->query, 'users/login/') !== 0)
        ) {
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
        $config = new DBAL\Configuration();
        $connectionParams = array(
            'dbname' => CONFIG_DB_NAME,
            'user' => CONFIG_DB_USER,
            'password' => CONFIG_DB_PASSWORD,
            'host' => CONFIG_DB_HOST,
            'driver' => 'pdo_mysql',
            'charset' => 'utf8'
        );
        self::$db = DBAL\DriverManager::getConnection($connectionParams, $config);

        define('DB_PRE', CONFIG_DB_PRE);

        // Sytemeinstellungen laden
        Core\Config::getSystemSettings();

        // Pfade zum Theme setzen
        define('DESIGN_PATH', ROOT_DIR . 'designs/' . CONFIG_DESIGN . '/');
        define('DESIGN_PATH_INTERNAL', ACP3_ROOT_DIR . 'designs/' . CONFIG_DESIGN . '/');
        define('DESIGN_PATH_ABSOLUTE', HOST_NAME . DESIGN_PATH);

        // Restliche Klassen instanziieren
        self::$view = new Core\View();
        self::$uri = new Core\URI(self::$db);
        self::$session = new Core\Session(self::$db, self::$uri, self::$view);
        self::$auth = new Core\Auth(self::$db, self::$session);
        self::$lang = new Core\Lang(self::$auth);
        self::$seo = new Core\SEO(self::$db, self::$lang, self::$uri, self::$view);
        self::$date = new Core\Date(self::$auth, self::$lang, self::$view);
        self::$breadcrumb = new Core\Breadcrumb(self::$db, self::$lang, self::$uri, self::$view);

        Core\Registry::set('Db', self::$db);
        Core\Registry::set('View', self::$view);
        Core\Registry::set('URI', self::$uri);
        Core\Registry::set('Session', self::$session);
        Core\Registry::set('Auth', self::$auth);
        Core\Registry::set('Lang', self::$lang);
        Core\Registry::set('SEO', self::$seo);
        Core\Registry::set('Date', self::$date);
        Core\Registry::set('Breadcrumb', self::$breadcrumb);

        Core\View::factory('Smarty');

        Core\ACL::initialize(self::$auth->getUserId());
    }

    /**
     * Gibt die Seite aus
     */
    public static function outputPage()
    {
        // Einige Template Variablen setzen
        self::$view->assign('PHP_SELF', PHP_SELF);
        self::$view->assign('REQUEST_URI', htmlentities($_SERVER['REQUEST_URI']));
        self::$view->assign('ROOT_DIR', ROOT_DIR);
        self::$view->assign('ROOT_DIR_ABSOLUTE', ROOT_DIR_ABSOLUTE);
        self::$view->assign('DESIGN_PATH', DESIGN_PATH);
        self::$view->assign('DESIGN_PATH_ABSOLUTE', DESIGN_PATH_ABSOLUTE);
        self::$view->assign('UA_IS_MOBILE', Core\Functions::isMobileBrowser());
        self::$view->assign('IN_ADM', defined('IN_ADM'));

        $langInfo = Core\XML::parseXmlFile(ACP3_ROOT_DIR . 'languages/' . self::$lang->getLanguage() . '/info.xml', '/language');
        self::$view->assign('LANG_DIRECTION', isset($langInfo['direction']) ? $langInfo['direction'] : 'ltr');
        self::$view->assign('LANG', CONFIG_LANG);

        self::checkForMaintenanceMode();

        // Aktuelle Datensatzposition bestimmen
        define('POS', Core\Validate::isNumber(self::$uri->page) && self::$uri->page >= 1 ? (int)(self::$uri->page - 1) * Core\Registry::get('Auth')->entries : 0);

        if (defined('IN_ADM') === true && self::$auth->isUser() === false && self::$uri->query !== 'users/login/') {
            $redirect_uri = base64_encode('acp/' . self::$uri->query);
            self::$uri->redirect('users/login/redirect_' . $redirect_uri);
        }

        if (Core\Modules::hasPermission(self::$uri->mod, self::$uri->file) === true) {
            $module = ucfirst(self::$uri->mod);
            $section = defined('IN_ADM') === true ? 'Admin' : 'Frontend';
            $className = "\\ACP3\\Modules\\" . $module . "\\Controller\\" . $section;
            $action = 'action' . preg_replace('/(\s+)/', '', ucwords(strtolower(str_replace('_', ' ', defined('IN_ADM') === true ? substr(self::$uri->file, 4) : self::$uri->file))));

            // Modul einbinden
            /** @var Controller $controller */
            $controller = new $className(
                self::$auth,
                self::$breadcrumb,
                self::$date,
                self::$db,
                self::$lang,
                self::$session,
                self::$uri,
                self::$view,
                self::$seo
            );

            $controller->$action();
            $controller->display();
        } else {
            self::$uri->redirect('errors/404');
        }
    }
}