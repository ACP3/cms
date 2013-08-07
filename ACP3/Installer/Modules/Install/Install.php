<?php

namespace ACP3\Installer\Modules\Install;

use ACP3\Installer\Core;

/**
 * Module controller of the Install installer module
 *
 * @author Tino Goratsch
 */
class Install extends \ACP3\Installer\Core\InstallerModuleController {

	public function __construct() {
		$navbar = array(
			'welcome' => array(
				'lang' => \ACP3\Core\Registry::get('Lang')->t('welcome'),
				'active' => false,
			),
			'licence' => array(
				'lang' => \ACP3\Core\Registry::get('Lang')->t('licence'),
				'active' => false,
			),
			'requirements' => array(
				'lang' => \ACP3\Core\Registry::get('Lang')->t('requirements'),
				'active' => false,
			),
			'configuration' => array(
				'lang' => \ACP3\Core\Registry::get('Lang')->t('configuration'),
				'active' => false,
			)
		);
		if (isset($navbar[\ACP3\Core\Registry::get('URI')->file]) === true) {
			$navbar[\ACP3\Core\Registry::get('URI')->file]['active'] = true;
		}
		\ACP3\Core\Registry::get('View')->assign('navbar', $navbar);
	}

	public function actionConfiguration() {
		if (isset($_POST['submit'])) {
			$config_path = ACP3_DIR . 'config.php';

			if (empty($_POST['db_host']))
				$errors['db-host'] = \ACP3\Core\Registry::get('Lang')->t('type_in_db_host');
			if (empty($_POST['db_user']))
				$errors['db-user'] = \ACP3\Core\Registry::get('Lang')->t('type_in_db_username');
			if (empty($_POST['db_name']))
				$errors['db-name'] = \ACP3\Core\Registry::get('Lang')->t('type_in_db_name');
			if (!empty($_POST['db_host']) && !empty($_POST['db_user']) && !empty($_POST['db_name'])) {
				try {
					$config = new \Doctrine\DBAL\Configuration();

					$connectionParams = array(
						'dbname' => $_POST['db_name'],
						'user' => $_POST['db_user'],
						'password' => $_POST['db_password'],
						'host' => $_POST['db_host'],
						'driver' => 'pdo_mysql',
						'charset' => 'utf8'
					);
					$db = \Doctrine\DBAL\DriverManager::getConnection($connectionParams, $config);
					$db->query('USE `' . $_POST['db_name'] . '`');
				} catch (Exception $e) {
					$errors[] = sprintf(\ACP3\Core\Registry::get('Lang')->t('db_connection_failed'), $e->getMessage());
				}
			}
			if (empty($_POST['user_name']))
				$errors['user-name'] = \ACP3\Core\Registry::get('Lang')->t('type_in_user_name');
			if ((empty($_POST['user_pwd']) || empty($_POST['user_pwd_wdh'])) ||
					(!empty($_POST['user_pwd']) && !empty($_POST['user_pwd_wdh']) && $_POST['user_pwd'] != $_POST['user_pwd_wdh']))
				$errors['user-pwd'] = \ACP3\Core\Registry::get('Lang')->t('type_in_pwd');
			if (\ACP3\Core\Validate::email($_POST['mail']) === false)
				$errors['mail'] = \ACP3\Core\Registry::get('Lang')->t('wrong_email_format');
			if (empty($_POST['date_format_long']))
				$errors['date-format-long'] = \ACP3\Core\Registry::get('Lang')->t('type_in_date_format');
			if (empty($_POST['date_format_short']))
				$errors['date-format-short'] = \ACP3\Core\Registry::get('Lang')->t('type_in_date_format');
			if (\ACP3\Core\Validate::timeZone($_POST['date_time_zone']) === false)
				$errors['date-time-zone'] = \ACP3\Core\Registry::get('Lang')->t('select_time_zone');
			if (is_file($config_path) === false || is_writable($config_path) === false)
				$errors[] = \ACP3\Core\Registry::get('Lang')->t('wrong_chmod_for_config_file');

			if (isset($errors)) {
				\ACP3\Core\Registry::get('View')->assign('error_msg', Core\Functions::errorBox($errors));
			} else {
				// Systemkonfiguration erstellen
				$config_file = array(
					'db_host' => $_POST['db_host'],
					'db_name' => $_POST['db_name'],
					'db_pre' => $_POST['db_pre'],
					'db_password' => $_POST['db_password'],
					'db_user' => $_POST['db_user'],
				);
				Core\Functions::writeConfigFile($config_file);

				// Doctrine DBAL Initialisieren
				$config = new \Doctrine\DBAL\Configuration();
				$connectionParams = array(
					'dbname' => $_POST['db_name'],
					'user' => $_POST['db_user'],
					'password' => $_POST['db_password'],
					'host' => $_POST['db_host'],
					'driver' => 'pdo_mysql',
					'charset' => 'utf8'
				);
				\ACP3\Core\Registry::set('Db', \Doctrine\DBAL\DriverManager::getConnection($connectionParams, $config));
				define('DB_PRE', $_POST['db_pre']);

				$bool = false;
				// Core-Module installieren
				$install_first = array('system', 'permissions', 'users');
				foreach ($install_first as $module) {
					$bool = Core\Functions::installModule($module);
					if ($bool === false) {
						\ACP3\Core\Registry::get('View')->assign('install_error', true);
						break;
					}
				}

				// "Normale" Module installieren
				if ($bool === true) {
					$mods_dir = scandir(MODULES_DIR);
					foreach ($mods_dir as $module) {
						if ($module !== '.' && $module !== '..' && in_array(strtolower($module), $install_first) === false) {
							$bool = Core\Functions::installModule($module);
							if ($bool === false) {
								\ACP3\Core\Registry::get('View')->assign('install_error', true);
								break;
							}
						}
					}
				}

				// Admin-User, Menüpunkte, News, etc. in die DB schreiben
				if ($bool === true) {
					$salt = \ACP3\Core\Functions::salt(12);
					$current_date = gmdate('Y-m-d H:i:s');

					$news_mod_id = \ACP3\Core\Registry::get('Db')->fetchColumn('SELECT id FROM ' . DB_PRE . 'modules WHERE name = ?', array('news'));
					$queries = array(
						"INSERT INTO `{pre}users` VALUES ('', 1, " . \ACP3\Core\Registry::get('Db')->quote($_POST["user_name"]) . ", '" . \ACP3\Core\Functions::generateSaltedPassword($salt, $_POST["user_pwd"]) . ":" . $salt . "', 0, '', '1', '', 0, '" . $_POST["mail"] . "', 0, '', '', '', '', '', '', '', '', 0, 0, " . \ACP3\Core\Registry::get('Db')->quote($_POST["date_format_long"]) . ", " . \ACP3\Core\Registry::get('Db')->quote($_POST["date_format_short"]) . ", '" . $_POST["date_time_zone"] . "', '" . LANG . "', '20', '', '" . $current_date . "');",
						'INSERT INTO `{pre}categories` VALUES (\'\', \'' . \ACP3\Core\Registry::get('Lang')->t('category_name') . '\', \'\', \'' . \ACP3\Core\Registry::get('Lang')->t('category_description') . '\', \'' . $news_mod_id . '\');',
						'INSERT INTO `{pre}news` VALUES (\'\', \'' . $current_date . '\', \'' . $current_date . '\', \'' . \ACP3\Core\Registry::get('Lang')->t('news_headline') . '\', \'' . \ACP3\Core\Registry::get('Lang')->t('news_text') . '\', \'1\', \'1\', \'1\', \'\', \'\', \'\', \'\');',
						'INSERT INTO `{pre}menu_items` VALUES (\'\', 1, 1, 1, 0, 1, 4, 1, \'' . \ACP3\Core\Registry::get('Lang')->t('pages_news') . '\', \'news\', 1);',
						'INSERT INTO `{pre}menu_items` VALUES (\'\', 1, 1, 1, 1, 2, 3, 1, \'' . \ACP3\Core\Registry::get('Lang')->t('pages_newsletter') . '\', \'newsletter\', 1);',
						'INSERT INTO `{pre}menu_items` VALUES (\'\', 1, 1, 3, 0, 5, 6, 1, \'' . \ACP3\Core\Registry::get('Lang')->t('pages_files') . '\', \'files\', 1);',
						'INSERT INTO `{pre}menu_items` VALUES (\'\', 1, 1, 4, 0, 7, 8, 1, \'' . \ACP3\Core\Registry::get('Lang')->t('pages_gallery') . '\', \'gallery\', 1);',
						'INSERT INTO `{pre}menu_items` VALUES (\'\', 1, 1, 5, 0, 9, 10, 1, \'' . \ACP3\Core\Registry::get('Lang')->t('pages_guestbook') . '\', \'guestbook\', 1);',
						'INSERT INTO `{pre}menu_items` VALUES (\'\', 1, 1, 6, 0, 11, 12, 1, \'' . \ACP3\Core\Registry::get('Lang')->t('pages_polls') . '\', \'polls\', 1);',
						'INSERT INTO `{pre}menu_items` VALUES (\'\', 1, 1, 7, 0, 13, 14, 1, \'' . \ACP3\Core\Registry::get('Lang')->t('pages_search') . '\', \'search\', 1);',
						'INSERT INTO `{pre}menu_items` VALUES (\'\', 1, 2, 8, 0, 15, 16, 1, \'' . \ACP3\Core\Registry::get('Lang')->t('pages_contact') . '\', \'contact\', 1);',
						'INSERT INTO `{pre}menu_items` VALUES (\'\', 2, 2, 9, 0, 17, 18, 1, \'' . \ACP3\Core\Registry::get('Lang')->t('pages_imprint') . '\', \'contact/imprint/\', 1);',
						'INSERT INTO `{pre}menus` VALUES (1, \'main\', \'' . \ACP3\Core\Registry::get('Lang')->t('pages_main') . '\');',
						'INSERT INTO `{pre}menus` VALUES (2, \'sidebar\', \'' . \ACP3\Core\Registry::get('Lang')->t('pages_sidebar') . '\');',
					);

					if (\ACP3\Core\ModuleInstaller::executeSqlQueries($queries) === false) {
						\ACP3\Core\Registry::get('View')->assign('install_error', true);
					}

					// Modulkonfigurationsdateien schreiben
					$system_settings = array(
						'cache_images' => true,
						'cache_minify' => 3600,
						'date_format_long' => \ACP3\Core\Functions::strEncode($_POST['date_format_long']),
						'date_format_short' => \ACP3\Core\Functions::strEncode($_POST['date_format_short']),
						'date_time_zone' => $_POST['date_time_zone'],
						'design' => 'acp3',
						'entries' => 20,
						'flood' => 30,
						'homepage' => 'news/list/',
						'lang' => LANG,
						'mailer_smtp_auth' => 0,
						'mailer_smtp_host' => '',
						'mailer_smtp_password' => '',
						'mailer_smtp_port' => 25,
						'mailer_smtp_security' => 'none',
						'mailer_smtp_user' => '',
						'mailer_type' => 'mail',
						'maintenance_mode' => 0,
						'maintenance_message' => \ACP3\Core\Registry::get('Lang')->t('offline_message'),
						'seo_aliases' => 1,
						'seo_meta_description' => '',
						'seo_meta_keywords' => '',
						'seo_mod_rewrite' => 1,
						'seo_robots' => 1,
						'seo_title' => !empty($_POST['seo_title']) ? $_POST['seo_title'] : 'ACP3',
						'version' => CONFIG_VERSION,
						'wysiwyg' => 'CKEditor'
					);
					\ACP3\Core\Config::setSettings('system', $system_settings);
					\ACP3\Core\Config::setSettings('users', array('mail' => $_POST['mail']));
					\ACP3\Core\Config::setSettings('contact', array('mail' => $_POST['mail'], 'disclaimer' => \ACP3\Core\Registry::get('Lang')->t('disclaimer')));
					\ACP3\Core\Config::setSettings('newsletter', array('mail' => $_POST['mail'], 'mailsig' => \ACP3\Core\Registry::get('Lang')->t('sincerely') . "\n\n" . \ACP3\Core\Registry::get('Lang')->t('newsletter_mailsig')));
				}

				\ACP3\Core\Registry::get('View')->setContentTemplate('install/result.tpl');
			}
		}
		if (isset($_POST['submit']) === false || isset($errors) === true && is_array($errors) === true) {
			// Zeitzonen
			\ACP3\Core\Registry::get('View')->assign('time_zones', \ACP3\Core\Date::getTimeZones(date_default_timezone_get()));

			$defaults = array(
				'db_host' => 'localhost',
				'db_pre' => 'acp3_',
				'db_user' => '',
				'db_name' => '',
				'user_name' => 'admin',
				'mail' => '',
				'date_format_long' => 'd.m.y, H:i',
				'date_format_short' => 'd.m.y',
				'seo_title' => 'ACP3',
			);

			\ACP3\Core\Registry::get('View')->assign('form', isset($_POST['submit']) ? $_POST : $defaults);
		}
	}

	public function actionRequirements() {
		define('REQUIRED_PHP_VERSION', '5.3.2');
		define('COLOR_ERROR', 'f00');
		define('COLOR_SUCCESS', '090');
		define('CLASS_ERROR', 'important');
		define('CLASS_SUCCESS', 'success');
		define('CLASS_WARNING', 'warning');

		// Allgemeine Voraussetzungen
		$requirements = array();
		$requirements[0]['name'] = \ACP3\Core\Registry::get('Lang')->t('php_version');
		$requirements[0]['color'] = version_compare(phpversion(), REQUIRED_PHP_VERSION, '>=') ? COLOR_SUCCESS : COLOR_ERROR;
		$requirements[0]['found'] = phpversion();
		$requirements[0]['required'] = REQUIRED_PHP_VERSION;
		$requirements[1]['name'] = \ACP3\Core\Registry::get('Lang')->t('pdo_extension');
		$requirements[1]['color'] = extension_loaded('pdo') && extension_loaded('pdo_mysql') ? COLOR_SUCCESS : COLOR_ERROR;
		$requirements[1]['found'] = \ACP3\Core\Registry::get('Lang')->t($requirements[1]['color'] == COLOR_SUCCESS ? 'on' : 'off');
		$requirements[1]['required'] = \ACP3\Core\Registry::get('Lang')->t('on');
		$requirements[2]['name'] = \ACP3\Core\Registry::get('Lang')->t('gd_library');
		$requirements[2]['color'] = extension_loaded('gd') ? COLOR_SUCCESS : COLOR_ERROR;
		$requirements[2]['found'] = \ACP3\Core\Registry::get('Lang')->t($requirements[2]['color'] == COLOR_SUCCESS ? 'on' : 'off');
		$requirements[2]['required'] = \ACP3\Core\Registry::get('Lang')->t('on');
		$requirements[3]['name'] = \ACP3\Core\Registry::get('Lang')->t('register_globals');
		$requirements[3]['color'] = ((bool) ini_get('register_globals')) ? COLOR_ERROR : COLOR_SUCCESS;
		$requirements[3]['found'] = \ACP3\Core\Registry::get('Lang')->t(((bool) ini_get('register_globals')) ? 'on' : 'off');
		$requirements[3]['required'] = \ACP3\Core\Registry::get('Lang')->t('off');
		$requirements[4]['name'] = \ACP3\Core\Registry::get('Lang')->t('safe_mode');
		$requirements[4]['color'] = ((bool) ini_get('safe_mode')) ? COLOR_ERROR : COLOR_SUCCESS;
		$requirements[4]['found'] = \ACP3\Core\Registry::get('Lang')->t(((bool) ini_get('safe_mode')) ? 'on' : 'off');
		$requirements[4]['required'] = \ACP3\Core\Registry::get('Lang')->t('off');

		\ACP3\Core\Registry::get('View')->assign('requirements', $requirements);

		$defaults = array('ACP3/config.php');

		// Uploadordner
		$uploads = scandir(UPLOADS_DIR);
		foreach ($uploads as $row) {
			$path = 'uploads/' . $row . '/';
			if ($row !== '.' && $row !== '..' && is_dir(ACP3_ROOT_DIR . $path) === true) {
				$defaults[] = $path;
			}
		}
		$files_dirs = array();
		$check_again = false;

		$i = 0;
		foreach ($defaults as $row) {
			$files_dirs[$i]['path'] = $row;
			// Überprüfen, ob es eine Datei oder ein Ordner ist
			if (is_file(ACP3_ROOT_DIR . $row) === true) {
				$files_dirs[$i]['class_1'] = CLASS_SUCCESS;
				$files_dirs[$i]['exists'] = \ACP3\Core\Registry::get('Lang')->t('found');
			} elseif (is_dir(ACP3_ROOT_DIR . $row) === true) {
				$files_dirs[$i]['class_1'] = CLASS_SUCCESS;
				$files_dirs[$i]['exists'] = \ACP3\Core\Registry::get('Lang')->t('found');
			} else {
				$files_dirs[$i]['class_1'] = CLASS_ERROR;
				$files_dirs[$i]['exists'] = \ACP3\Core\Registry::get('Lang')->t('not_found');
			}
			$files_dirs[$i]['class_2'] = is_writable(ACP3_ROOT_DIR . $row) === true ? CLASS_SUCCESS : CLASS_ERROR;
			$files_dirs[$i]['writable'] = $files_dirs[$i]['class_2'] === CLASS_SUCCESS ? \ACP3\Core\Registry::get('Lang')->t('writable') : \ACP3\Core\Registry::get('Lang')->t('not_writable');
			if ($files_dirs[$i]['class_1'] == CLASS_ERROR || $files_dirs[$i]['class_2'] == CLASS_ERROR) {
				$check_again = true;
			}
			$i++;
		}
		\ACP3\Core\Registry::get('View')->assign('files_dirs', $files_dirs);

		// PHP Einstellungen
		$php_settings = array();
		$php_settings[0]['setting'] = \ACP3\Core\Registry::get('Lang')->t('maximum_uploadsize');
		$php_settings[0]['class'] = ini_get('post_max_size') > 0 ? CLASS_SUCCESS : CLASS_WARNING;
		$php_settings[0]['value'] = ini_get('post_max_size');
		$php_settings[1]['setting'] = \ACP3\Core\Registry::get('Lang')->t('magic_quotes');
		$php_settings[1]['class'] = (bool) ini_get('magic_quotes_gpc') ? CLASS_WARNING : CLASS_SUCCESS;
		$php_settings[1]['value'] = \ACP3\Core\Registry::get('Lang')->t((bool) ini_get('magic_quotes_gpc') ? 'on' : 'off');
		\ACP3\Core\Registry::get('View')->assign('php_settings', $php_settings);

		foreach ($requirements as $row) {
			if ($row['color'] !== COLOR_SUCCESS) {
				\ACP3\Core\Registry::get('View')->assign('stop_install', true);
			}
		}

		if ($check_again === true) {
			\ACP3\Core\Registry::get('View')->assign('check_again', true);
		}
	}

	public function actionLicence() {
		\ACP3\Core\Registry::get('View')->setContentTemplate('install/licence.tpl');
	}

	public function actionWelcome() {
		\ACP3\Core\Registry::get('View')->setContentTemplate('install/welcome.tpl');
	}

}