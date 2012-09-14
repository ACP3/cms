<?php
/**
 * @author Tino Goratsch
 * @package ACP3
 * @subpackage Core
 */

use Doctrine\Common\ClassLoader;

class ACP3_CMS {
	public static $auth = null;
	public static $breadcrumb = null;
	public static $date = null;
	public static $db = null;
	public static $db2 = null;
	public static $lang = null;
	public static $session = null;
	public static $tpl = null;
	public static $uri = null;
	public static $view = null;
	private static $content = '';

	/**
	 * Weist dem Template den auszugebenden Inhalt zu
	 *
	 * @param string $data
	 */
	public static function setContent($data)
	{
		self::$content = $data;
	}

	/**
	 * Fügt weitere Daten an den Seiteninhalt an
	 *
	 * @param string $data
	 */
	public static function appendContent($data)
	{
		self::$content.= $data;
	}

	/**
	 * Gibt den auszugebenden Seiteninhalt zurück
	 *
	 * @return string
	 */
	public static function getContent()
	{
		return self::$content;
	}

	/**
	 * Führt alle nötigen Schritte aus, um die Seite anzuzeigen
	 */
	public static function run()
	{
		self::startupChecks();
		self::defineDirConstants();
		self::includeAutoLoader();
		self::initializeDoctrineDBAL();
		self::initializeClasses();
		self::outputPage();
	}

	/**
	 * Überprüft, ob die config.php existiert
	 */
	public static function startupChecks()
	{
		// register_globals OFF Emulation
		require ACP3_ROOT . 'includes/globals.php';

		// DB-Config des ACP3 laden
		$path = ACP3_ROOT . 'includes/config.php';
		if (is_file($path) === false || filesize($path) === 0) {
			exit('The ACP3 is not correctly installed. Please navigate to the <a href="' . ROOT_DIR . 'installation/">installation wizard</a> and follow its instructions.');
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
		define('MODULES_DIR', ACP3_ROOT . 'modules/');
		define('INCLUDES_DIR', ACP3_ROOT . 'includes/');
		define('CLASSES_DIR', INCLUDES_DIR . 'classes/');
		define('LIBRARIES_DIR', ACP3_ROOT . 'libraries/');
		define('UPLOADS_DIR', ACP3_ROOT . 'uploads/');
		define('CACHE_DIR', UPLOADS_DIR . 'cache/');
	}

	/**
	 * Klassen Autoloader inkludieren
	 */
	public static function includeAutoLoader()
	{
		require INCLUDES_DIR . 'autoload.php';

		require LIBRARIES_DIR . 'Doctrine/Common/ClassLoader.php';

		$classLoader = new ClassLoader('Doctrine', LIBRARIES_DIR);
		$classLoader->register();
	}

	/**
	 * Überprüfen, ob der Wartungsmodus aktiv ist
	 */
	public static function checkForMaintenanceMode()
	{
		if (defined('IN_ADM') === false && (bool) CONFIG_MAINTENANCE_MODE === true) {
			self::$view->assign('PAGE_TITLE', CONFIG_SEO_TITLE);
			self::$view->assign('CONTENT', CONFIG_MAINTENANCE_MESSAGE);
			self::$view->displayTemplate('maintenance.tpl');
			exit;
		}
	}

	/**
	 * Initialisieren von Doctrine
	 */
	public static function initializeDoctrineDBAL()
	{
		require_once INCLUDES_DIR . 'config.php';

		// Wenn der DEBUG Modus aktiv ist, Fehler ausgeben
		error_reporting(defined('DEBUG') === true && DEBUG === true ? E_ALL : 0);

		$config = new \Doctrine\DBAL\Configuration();

		$connectionParams = array(
			'dbname' => CONFIG_DB_NAME,
			'user' => CONFIG_DB_USER,
			'password' => CONFIG_DB_PASSWORD,
			'host' => CONFIG_DB_HOST,
			'driver' => 'pdo_mysql',
			'charset' => 'utf8'
		);
		self::$db2 = Doctrine\DBAL\DriverManager::getConnection($connectionParams, $config);

		define('DB_PRE', CONFIG_DB_PRE);
	}

	/**
	 * Initialisierung der alten DB-Klasse
	 */
	public static function initializeLegacyDatabase()
	{
		require_once INCLUDES_DIR . 'config.php';

		// Klassen initialisieren
		self::$db = new ACP3_DB();
		$handle = self::$db->connect(CONFIG_DB_HOST, CONFIG_DB_NAME, CONFIG_DB_USER, CONFIG_DB_PASSWORD, CONFIG_DB_PRE);
		if ($handle !== true)
			exit($handle);
	}

	/**
	 * Initialisieren der anderen Klassen
	 */
	public static function initializeClasses()
	{
		// Sytemeinstellungen laden
		ACP3_Config::getSystemSettings();

		// Standardzeitzone festlegen
		date_default_timezone_set(CONFIG_DATE_TIME_ZONE);

		define('DESIGN_PATH', ROOT_DIR . 'designs/' . CONFIG_DESIGN . '/');
		define('DESIGN_PATH_INTERNAL', ACP3_ROOT . 'designs/' . CONFIG_DESIGN . '/');

		self::$view = new ACP3_View();
		ACP3_View::factory('Smarty');

		self::$uri = new ACP3_URI();

		// Klassen initialisieren
		self::$session = new ACP3_Session();
		self::$auth = new ACP3_Auth();
		self::$lang = new ACP3_Lang();
		self::$date = new ACP3_Date();
		self::$breadcrumb = new ACP3_Breadcrumb();

		ACP3_ACL::initialize(self::$auth->getUserId());

		require INCLUDES_DIR . 'functions.php';

		// Einige Template Variablen setzen
		self::$view->assign('PHP_SELF', PHP_SELF);
		self::$view->assign('REQUEST_URI', htmlentities($_SERVER['REQUEST_URI']));
		self::$view->assign('ROOT_DIR', ROOT_DIR);
		self::$view->assign('DESIGN_PATH', DESIGN_PATH);
		self::$view->assign('LANG', CONFIG_LANG);
		self::$view->assign('UA_IS_MOBILE', isMobileBrowser());
	}

	/**
	 * Gibt die Seite aus
	 */
	public static function outputPage() {
		self::checkForMaintenanceMode();

		// Aktuelle Datensatzposition bestimmen
		define('POS', (int) (ACP3_Validate::isNumber(self::$uri->page) && self::$uri->page >= 1 ? (self::$uri->page - 1) * self::$auth->entries : 0));

		if (self::$auth->isUser() === false && defined('IN_ADM') === true && self::$uri->query !== 'users/login') {
			$redirect_uri = base64_encode('acp/' . self::$uri->query);
			self::$uri->redirect('users/login/redirect_' . $redirect_uri);
		}

		switch (ACP3_Modules::check()) {
			// Seite ausgeben
			case 1:
				require MODULES_DIR . self::$uri->mod . '/' . self::$uri->file . '.php';

				if (self::$view->getNoOutput() === false) {
					// Evtl. gesetzten Content-Type des Servers überschreiben
					header(self::$view->getContentType());

					if (self::$view->getLayout() !== '') {
						self::$view->assign('PAGE_TITLE', CONFIG_SEO_TITLE);
						self::$view->assign('TITLE', self::$breadcrumb->output(2));
						self::$view->assign('BREADCRUMB', self::$breadcrumb->output());
						self::$view->assign('META', ACP3_SEO::getMetaTags());
						self::$view->assign('CONTENT', self::$content);

						$minify = self::$view->buildMinifyLink();
						$file = self::$view->getLayout();
						$layout = substr($file, 0, strpos($file, '.'));
						self::$view->assign('MIN_STYLESHEET', sprintf($minify, 'css') . ($layout !== 'layout' ? '&amp;layout=' . $layout : ''));
						self::$view->assign('MIN_JAVASCRIPT', sprintf($minify, 'js'));

						self::$view->displayTemplate($file);
					} else {
						echo self::$content;
					}
				}
				break;
			// Kein Zugriff auf die Seite
			case 0:
				self::$uri->redirect('errors/403');
				break;
			// Seite nicht gefunden
			default:
				self::$uri->redirect('errors/404');
		}
	}
}