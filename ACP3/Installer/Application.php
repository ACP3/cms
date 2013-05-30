<?php

namespace ACP3\Installer;

/**
 * Front Controller of the Installer
 *
 * @author Tino Goratsch
 */
class Application {

	/**
	 * run Methode für den Installer
	 */
	public static function runInstaller() {
		self::defineDirConstants();
		self::includeAutoLoader();
		self::initializeInstallerClasses();
		self::outputPage();
	}

	/**
	 * rund() Methode für den Database Updater
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
			define('CONFIG_SEO_ALIASES', false);
			define('CONFIG_SEO_MOD_REWRITE', false);
		}
	}

	/**
	 * Klassen Autoloader inkludieren
	 */
	public static function includeAutoLoader() {
		require VENDOR_DIR . 'autoload.php';
	}

	/**
	 * Initialisieren der Klassen für den Installer
	 */
	public static function initializeInstallerClasses() {
		\ACP3\Core\Registry::set('View', new \ACP3\Core\View());
		\ACP3\Core\Registry::set('URI', new \ACP3\Core\URI('install', 'welcome'));
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
		$config = new \Doctrine\DBAL\Configuration();
		$connectionParams = array(
			'dbname' => CONFIG_DB_NAME,
			'user' => CONFIG_DB_USER,
			'password' => CONFIG_DB_PASSWORD,
			'host' => CONFIG_DB_HOST,
			'driver' => 'pdo_mysql',
			'charset' => 'utf8'
		);
		\ACP3\Core\Registry::set('Db', \Doctrine\DBAL\DriverManager::getConnection($connectionParams, $config));
		define('DB_PRE', CONFIG_DB_PRE);

		// Sytemeinstellungen laden
		\ACP3\Core\Config::getSystemSettings();

		\ACP3\Core\Registry::set('View', new \ACP3\Core\View());
		\ACP3\Core\Registry::set('URI', new \ACP3\Core\URI('update', 'db_update'));
		$params = array(
			'compile_id' => 'installer',
			'plugins_dir' => INSTALLER_CLASSES_DIR . 'SmartyHelpers/',
			'template_dir' => array(DESIGN_PATH_INTERNAL, INSTALLER_MODULES_DIR)
		);
		\ACP3\Core\View::factory('Smarty', $params);
	}

	/**
	 * Gibt die Seite aus
	 */
	public static function outputPage() {
		$view = \ACP3\Core\Registry::get('View');
		$uri = \ACP3\Core\Registry::get('URI');

		if (!empty($_POST['lang'])) {
			setcookie('ACP3_INSTALLER_LANG', $_POST['lang'], time() + 3600, '/');
			$uri->redirect($uri->mod . '/' . $uri->file);
		}

		if (!empty($_COOKIE['ACP3_INSTALLER_LANG']) && !preg_match('=/=', $_COOKIE['ACP3_INSTALLER_LANG']) &&
				is_file(ACP3_ROOT_DIR . 'installation/languages/' . $_COOKIE['ACP3_INSTALLER_LANG'] . '.xml') === true) {
			define('LANG', $_COOKIE['ACP3_INSTALLER_LANG']);
		} else {
			define('LANG', \ACP3\Core\Lang::parseAcceptLanguage());
		}
		\ACP3\Core\Registry::set('Lang', new Core\InstallerLang(LANG));

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
		$view->assign('LANG', LANG);

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

}