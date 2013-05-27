<?php

namespace ACP3;

/**
 * Front Controller of the CMS
 * 
 * @author Tino Goratsch
 */
class Application {

	/**
	 * Führt alle nötigen Schritte aus, um die Seite anzuzeigen
	 */
	public static function run() {
		self::defineDirConstants();
		self::startupChecks();
		self::includeAutoLoader();
		self::initializeClasses();
		self::outputPage();
	}

	/**
	 * Überprüft, ob die config.php existiert
	 */
	public static function startupChecks() {
		// Standardzeitzone festlegen
		date_default_timezone_set('UTC');

		// DB-Config des ACP3 laden
		$path = ACP3_DIR . 'config.php';
		if (is_file($path) === false || filesize($path) === 0) {
			exit('The ACP3 is not correctly installed. Please navigate to the <a href="' . ROOT_DIR . 'installation/">installation wizard</a> and follow its instructions.');
			// Wenn alles okay ist, config.php einbinden und error_reporting setzen
		} else {
			require_once ACP3_DIR . 'config.php';

			// Wenn der DEBUG Modus aktiv ist, Fehler ausgeben
			error_reporting(defined('DEBUG') === true && DEBUG === true ? E_ALL : 0);
		}
	}

	/**
	 * Einige Pfadkonstanten definieren
	 */
	public static function defineDirConstants() {
		define('PHP_SELF', htmlentities($_SERVER['SCRIPT_NAME']));
		$php_self = dirname(PHP_SELF);
		define('ROOT_DIR', $php_self !== '/' ? $php_self . '/' : '/');
		define('ACP3_DIR', ACP3_ROOT_DIR . 'ACP3/');
		define('CLASSES_DIR', ACP3_DIR . 'Core/');
		define('MODULES_DIR', ACP3_DIR . 'Modules/');
		define('LIBRARIES_DIR', ACP3_ROOT_DIR . 'libraries/');
		define('UPLOADS_DIR', ACP3_ROOT_DIR . 'uploads/');
		define('CACHE_DIR', UPLOADS_DIR . 'cache/');
	}

	/**
	 * Klassen Autoloader inkludieren
	 */
	public static function includeAutoLoader() {
		require_once LIBRARIES_DIR . 'Doctrine/Common/ClassLoader.php';

		$clACP3 = new \Doctrine\Common\ClassLoader('ACP3', ACP3_ROOT_DIR);
		$clACP3->register();

		$clDoctrine = new \Doctrine\Common\ClassLoader('Doctrine', LIBRARIES_DIR);
		$clDoctrine->register();
	}

	/**
	 * Überprüfen, ob der Wartungsmodus aktiv ist
	 */
	public static function checkForMaintenanceMode() {
		if ((bool) CONFIG_MAINTENANCE_MODE === true &&
				(defined('IN_ADM') === false && strpos(Core\Registry::get('URI')->query, 'users/login/') !== 0)) {
			Core\Registry::get('View')->assign('PAGE_TITLE', CONFIG_SEO_TITLE);
			Core\Registry::get('View')->assign('CONTENT', CONFIG_MAINTENANCE_MESSAGE);
			Core\Registry::get('View')->displayTemplate('system/maintenance.tpl');
			exit;
		}
	}

	/**
	 * Initialisieren der anderen Klassen
	 */
	public static function initializeClasses() {
		$config = new \Doctrine\DBAL\Configuration();
		$connectionParams = array(
			'dbname' => CONFIG_DB_NAME,
			'user' => CONFIG_DB_USER,
			'password' => CONFIG_DB_PASSWORD,
			'host' => CONFIG_DB_HOST,
			'driver' => 'pdo_mysql',
			'charset' => 'utf8'
		);
		Core\Registry::set('Db', \Doctrine\DBAL\DriverManager::getConnection($connectionParams, $config));
		define('DB_PRE', CONFIG_DB_PRE);

		// Sytemeinstellungen laden
		Core\Config::getSystemSettings();

		// Pfade zum Theme setzen
		define('DESIGN_PATH', ROOT_DIR . 'designs/' . CONFIG_DESIGN . '/');
		define('DESIGN_PATH_INTERNAL', ACP3_ROOT_DIR . 'designs/' . CONFIG_DESIGN . '/');

		// Restliche Klassen instanziieren
		$classes = array('View', 'URI', 'Session', 'Auth', 'Lang', 'Date', 'Breadcrumb');

		foreach ($classes as $class) {
			$className = "\\ACP3\\Core\\" . $class;
			Core\Registry::set($class, new $className());
		}
		Core\View::factory('Smarty');
		Core\ACL::initialize(Core\Registry::get('Auth')->getUserId());
	}

	/**
	 * Gibt die Seite aus
	 */
	public static function outputPage() {
		$view = Core\Registry::get('View');
		$uri = Core\Registry::get('URI');

		// Einige Template Variablen setzen
		$view->assign('PHP_SELF', PHP_SELF);
		$view->assign('REQUEST_URI', htmlentities($_SERVER['REQUEST_URI']));
		$view->assign('ROOT_DIR', ROOT_DIR);
		$view->assign('DESIGN_PATH', DESIGN_PATH);
		$view->assign('UA_IS_MOBILE', Core\Functions::isMobileBrowser());
		$view->assign('IN_ADM', defined('IN_ADM') ? true : false);

		$lang_info = Core\XML::parseXmlFile(ACP3_ROOT_DIR . 'languages/' . Core\Registry::get('Lang')->getLanguage() . '/info.xml', '/language');
		$view->assign('LANG_DIRECTION', isset($lang_info['direction']) ? $lang_info['direction'] : 'ltr');
		$view->assign('LANG', CONFIG_LANG);

		self::checkForMaintenanceMode();

		// Aktuelle Datensatzposition bestimmen
		if (Core\Validate::isNumber($uri->page) && $uri->page >= 1)
			define('POS', (int) ($uri->page - 1) * Core\Registry::get('Auth')->entries);
		else
			define('POS', 0);

		if (defined('IN_ADM') === true && Core\Registry::get('Auth')->isUser() === false && $uri->query !== 'users/login/') {
			$redirect_uri = base64_encode('acp/' . $uri->query);
			$uri->redirect('users/login/redirect_' . $redirect_uri);
		}

		if (Core\Modules::hasPermission($uri->mod, $uri->file) === true) {
			$module = ucfirst($uri->mod);
			$section = defined('IN_ADM') === true ? 'Admin' : 'Frontend';
			$className = "\\ACP3\\Modules\\" . $module . "\\" . $module . $section;
			$action = 'action' . preg_replace('/(\s+)/', '', ucwords(strtolower(str_replace('_', ' ', defined('IN_ADM') === true ? substr($uri->file, 4) : $uri->file))));

			// Modul einbinden
			$mod = new $className();
			$mod->$action();
			$mod->display();
		} else {
			$uri->redirect('errors/404');
		}
	}

}