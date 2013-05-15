<?php

/**
 * @author Tino Goratsch
 * @package ACP3
 * @subpackage Core
 */

namespace ACP3\Installer;

class Installer {

	/**
	 * Pimple Dependency Injector
	 *
	 * @var \ACP3\Core\Pimple
	 */
	public static $injector;

	/**
	 * Führt alle nötigen Schritte aus, um die Seite anzuzeigen
	 */
	public static function runInstaller() {
		self::defineDirConstants();
		self::includeAutoLoader();
		self::initializeInstallerClasses();
		self::outputPage();
	}
	/**
	 * 
	 */
	public static function runUpdater() {
		self::defineDirConstants();
		self::startupChecks();
		self::includeAutoLoader();
		self::initializeUpdaterClasses();
		self::outputPage();
	}

	/**
	 * Überprüft, ob die config.php existiert
	 */
	public static function startupChecks() {
		// Standardzeitzone festlegen
		date_default_timezone_set('UTC');

		error_reporting(E_ALL);

		if (defined('IN_UPDATER') === true) {
			// DB-Config des ACP3 laden
			$path = ACP3_DIR . 'config.php';
			if (is_file($path) === false || filesize($path) === 0) {
				exit('The ACP3 is not correctly installed. Please navigate to the <a href="' . ROOT_DIR . 'installation/">installation wizard</a> and follow its instructions.');
				// Wenn alles okay ist, config.php einbinden und error_reporting setzen
			} else {
				require_once ACP3_DIR . 'config.php';
			}
		}
	}

	/**
	 * Einige Pfadkonstanten definieren
	 */
	public static function defineDirConstants() {
		define('PHP_SELF', htmlentities($_SERVER['SCRIPT_NAME']));
		$php_self = dirname(PHP_SELF);
		define('ROOT_DIR', substr($php_self !== '/' ? $php_self . '/' : '/', 0, -13));
		define('INSTALLER_ROOT_DIR', $php_self !== '/' ? $php_self . '/' : '/');
		define('ACP3_DIR', ACP3_ROOT_DIR . 'ACP3/');
		define('CLASSES_DIR', ACP3_DIR . 'Core/');
		define('MODULES_DIR', ACP3_DIR . 'Modules/');
		define('LIBRARIES_DIR', ACP3_ROOT_DIR . 'libraries/');
		define('UPLOADS_DIR', ACP3_ROOT_DIR . 'uploads/');
		define('CACHE_DIR', UPLOADS_DIR . 'cache/');

		define('INSTALLER_DIR', ACP3_ROOT_DIR . 'installation/');
		define('INSTALLER_MODULES_DIR', ACP3_DIR . 'Installer/Modules/');
		define('INSTALLER_CLASSES_DIR', ACP3_DIR . 'Installer/Core/');
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
	 * Initialisieren der Klassen für den Installer
	 */
	public static function initializeInstallerClasses() {
		// DI
		self::$injector = new \ACP3\Core\Pimple();

		// Pfade zum Theme setzen
		define('DESIGN_PATH', INSTALLER_DIR . 'design/');
		define('DESIGN_PATH_INTERNAL', ACP3_ROOT_DIR . 'installation/design/');

		self::$injector['View'] = new \ACP3\Core\View();
		self::$injector['URI'] = new \ACP3\Core\URI('install', 'welcome');
		$params = array(
			'compile_id' => 'installer',
			'plugins_dir' => INSTALLER_CLASSES_DIR . 'SmartyHelpers/',
			'template_dir' => array(DESIGN_PATH_INTERNAL, INSTALLER_MODULES_DIR)
		);
		\ACP3\Core\View::factory('Smarty', $params);
	}

	/**
	 * Initialisieren der Klassen für den Updater
	 */
	public static function initializeUpdaterClasses() {
		// DI
		self::$injector = new \ACP3\Core\Pimple();

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
		$params = array(
			'compile_id' => 'installer',
			'plugins_dir' => INSTALLER_CLASSES_DIR . 'SmartyHelpers/',
			'template_dir' => array(DESIGN_PATH_INTERNAL, INSTALLER_MODULES_DIR)
		);
		\ACP3\Core\View::factory('Smarty', $params);
		Core\ACL::initialize(self::$injector['Auth']->getUserId());
	}

	/**
	 * Gibt die Seite aus
	 */
	public static function outputPage() {
		if (!empty($_POST['lang'])) {
			setcookie('ACP3_INSTALLER_LANG', $_POST['lang'], time() + 3600, '/');
			self::$injector['URI']->redirect(self::$injector['URI']->mod . '/' . self::$injector['URI']->file);
		}

		if (!empty($_COOKIE['ACP3_INSTALLER_LANG']) && !preg_match('=/=', $_COOKIE['ACP3_INSTALLER_LANG']) &&
			is_file(ACP3_ROOT_DIR . 'installation/languages/' . $_COOKIE['ACP3_INSTALLER_LANG'] . '.xml') === true) {
			define('LANG', $_COOKIE['ACP3_INSTALLER_LANG']);
		} else {
			define('LANG', \ACP3\Core\Lang::parseAcceptLanguage());
		}
		self::$injector['Lang'] = new Core\InstallerLang(LANG);

		// Einige Template Variablen setzen
		self::$injector['View']->assign('LANGUAGES', Core\Functions::languagesDropdown(LANG));
		self::$injector['View']->assign('PHP_SELF', PHP_SELF);
		self::$injector['View']->assign('REQUEST_URI', htmlentities($_SERVER['REQUEST_URI']));
		self::$injector['View']->assign('ROOT_DIR', ROOT_DIR);
		self::$injector['View']->assign('INSTALLER_ROOT_DIR', INSTALLER_ROOT_DIR);
		self::$injector['View']->assign('DESIGN_PATH', DESIGN_PATH);
		self::$injector['View']->assign('UA_IS_MOBILE', \ACP3\Core\Functions::isMobileBrowser());

		$lang_info = \ACP3\Core\XML::parseXmlFile(INSTALLER_DIR . 'languages/' . self::$injector['Lang']->getLanguage() . '.xml', '/language/info');
		self::$injector['View']->assign('LANG_DIRECTION', isset($lang_info['direction']) ? $lang_info['direction'] : 'ltr');
		self::$injector['View']->assign('LANG', LANG);

		$module = ucfirst(self::$injector['URI']->mod);
		$className = "\\ACP3\\Installer\\Modules\\" . $module . "\\" . $module;
		$action = 'action' . ucfirst(self::$injector['URI']->file);

		if (method_exists($className, $action) === true) {
			// Modul einbinden
			$mod = new $className(self::$injector);
			$mod->$action();
			$mod->display();
		} else {
			self::$injector['URI']->redirect('errors/404');
		}
	}

}