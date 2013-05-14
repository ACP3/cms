<?php

/**
 * @author Tino Goratsch
 * @package ACP3
 * @subpackage Core
 */

namespace ACP3;

class CMS {

	/**
	 * Pimple Dependency Injector
	 *
	 * @var \ACP3\Core\Pimple
	 */
	public static $injector;

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
				(defined('IN_ADM') === false && strpos(self::$injector['URI']->query, 'users/login/') !== 0)) {
			self::$injector['View']->assign('PAGE_TITLE', CONFIG_SEO_TITLE);
			self::$injector['View']->assign('CONTENT', CONFIG_MAINTENANCE_MESSAGE);
			self::$injector['View']->displayTemplate('system/maintenance.tpl');
			exit;
		}
	}

	/**
	 * Initialisieren der anderen Klassen
	 */
	public static function initializeClasses() {
		// DI
		self::$injector = new Core\Pimple();

		$config = new \Doctrine\DBAL\Configuration();
		$connectionParams = array(
			'dbname' => CONFIG_DB_NAME,
			'user' => CONFIG_DB_USER,
			'password' => CONFIG_DB_PASSWORD,
			'host' => CONFIG_DB_HOST,
			'driver' => 'pdo_mysql',
			'charset' => 'utf8'
		);
		self::$injector['Db'] = \Doctrine\DBAL\DriverManager::getConnection($connectionParams, $config);
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
			self::$injector[$class] = new $className();
		}
		Core\View::factory('Smarty');
		Core\ACL::initialize(self::$injector['Auth']->getUserId());
	}

	/**
	 * Gibt die Seite aus
	 */
	public static function outputPage() {
		// Einige Template Variablen setzen
		self::$injector['View']->assign('PHP_SELF', PHP_SELF);
		self::$injector['View']->assign('REQUEST_URI', htmlentities($_SERVER['REQUEST_URI']));
		self::$injector['View']->assign('ROOT_DIR', ROOT_DIR);
		self::$injector['View']->assign('DESIGN_PATH', DESIGN_PATH);
		self::$injector['View']->assign('UA_IS_MOBILE', Core\Functions::isMobileBrowser());
		self::$injector['View']->assign('IN_ADM', defined('IN_ADM') ? true : false);

		$lang_info = Core\XML::parseXmlFile(ACP3_ROOT_DIR . 'languages/' . self::$injector['Lang']->getLanguage() . '/info.xml', '/language');
		self::$injector['View']->assign('LANG_DIRECTION', isset($lang_info['direction']) ? $lang_info['direction'] : 'ltr');
		self::$injector['View']->assign('LANG', CONFIG_LANG);

		self::checkForMaintenanceMode();

		// Aktuelle Datensatzposition bestimmen
		if (Core\Validate::isNumber(self::$injector['URI']->page) && self::$injector['URI']->page >= 1)
			define('POS', (int) (self::$injector['URI']->page - 1) * self::$injector['Auth']->entries);
		else
			define('POS', 0);

		if (defined('IN_ADM') === true && self::$injector['Auth']->isUser() === false && self::$injector['URI']->query !== 'users/login/') {
			$redirect_uri = base64_encode('acp/' . self::$injector['URI']->query);
			self::$injector['URI']->redirect('users/login/redirect_' . $redirect_uri);
		}

		if (Core\Modules::check(self::$injector['URI']->mod, self::$injector['URI']->file) === true) {
			$module = ucfirst(self::$injector['URI']->mod);
			$section = defined('IN_ADM') === true ? 'Admin' : 'Frontend';
			$className = "\\ACP3\\Modules\\" . $module . "\\" . $module . $section;
			$action = 'action' . ucfirst(defined('IN_ADM') === true ? substr(self::$injector['URI']->file, 4) : self::$injector['URI']->file);

			// Modul einbinden
			$mod = new $className(self::$injector);
			$mod->$action();
			$mod->display();
		} else {
			self::$injector['URI']->redirect('errors/404');
		}
	}

}