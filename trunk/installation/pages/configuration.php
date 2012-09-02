<?php
/**
 * Installer
 *
 * @author Tino Goratsch
 * @package ACP3
 * @subpackage Installer
 */

if (defined('IN_INSTALL') === false)
	exit;

if (isset($_POST['submit'])) {
	$config_path = ACP3_ROOT . 'includes/config.php';
	$test_db = new ACP3_DB();

	if (empty($_POST['db_host']))
		$errors[] = $lang->t('type_in_db_host');
	if (empty($_POST['db_user']))
		$errors[] = $lang->t('type_in_db_username');
	if (empty($_POST['db_name']))
		$errors[] = $lang->t('type_in_db_name');
	if (!empty($_POST['db_host']) && !empty($_POST['db_user']) && !empty($_POST['db_name']) &&
		$test_db->connect($_POST['db_host'], $_POST['db_name'], $_POST['db_user'], $_POST['db_password']) !== true)
		$errors[] = $lang->t('db_connection_failed');
	if (empty($_POST['user_name']))
		$errors[] = $lang->t('type_in_user_name');
	if ((empty($_POST['user_pwd']) || empty($_POST['user_pwd_wdh'])) ||
		(!empty($_POST['user_pwd']) && !empty($_POST['user_pwd_wdh']) && $_POST['user_pwd'] != $_POST['user_pwd_wdh']))
		$errors[] = $lang->t('type_in_pwd');
	if (ACP3_Validate::email($_POST['mail']) === false)
		$errors[] = $lang->t('wrong_email_format');
	if (empty($_POST['date_format_long']) || empty($_POST['date_format_short']))
		$errors[] = $lang->t('type_in_date_format');
	if (ACP3_Validate::timeZone($_POST['date_time_zone']) === false)
		$errors[] = $lang->t('select_time_zone');
	if (is_file($config_path) === false || is_writable($config_path) === false)
		$errors[] = $lang->t('wrong_chmod_for_config_file');

	if (isset($errors)) {
		$tpl->assign('errors', $errors);
		$tpl->assign('error_msg', $tpl->fetch('error_box.tpl'));
	} else {
		// Systemkonfiguration erstellen
		$config = array(
			'db_host' => $_POST['db_host'],
			'db_name' => $_POST['db_name'],
			'db_pre' => $_POST['db_pre'],
			'db_password' => $_POST['db_password'],
			'db_user' => $_POST['db_user'],
		);
		writeConfigFile($config);

		require INCLUDES_DIR . 'bootstrap.php';

		ACP3_CMS::startupChecks();
		ACP3_CMS::initializeDatabase();

		$bool = false;
		// Core-Module installieren
		$install_first = array('system', 'permissions', 'users');
		foreach ($install_first as $module) {
			$bool = installModule($module);
			if ($bool === false) {
				$tpl->assign('install_error', true);
				break;
			}
		}

		// "Normale" Module installieren
		if ($bool === true) {
			$mods_dir = scandir(MODULES_DIR);
			foreach ($mods_dir as $module) {
				if ($module !== '.' && $module !== '..' && in_array($module, $install_first) === false) {
					$bool = installModule($module);
					if ($bool === false) {
						$tpl->assign('install_error', true);
						break;
					}
				}
			}
		}

		// Admin-User, Menüpunkte, News, etc. in die DB schreiben
		if ($bool === true) {
			$salt = salt(12);
			$current_date = gmdate('Y-m-d H:i:s');

			$news_mod_id = ACP3_CMS::$db->select('id', 'modules', 'name = \'news\'');
			$queries = array(
				'INSERT INTO `{pre}users` VALUES (\'\', 1, \'' . ACP3_CMS::$db->escape($_POST['user_name']) . '\', \'' . generateSaltedPassword($salt, $_POST['user_pwd']) . ':' . $salt . '\', \'0\', \':1\', \'1:1\', \':1\', \'1\', \'' . $_POST['mail'] . ':1\', \':1\', \':1\', \':1\', \':1\', \'' . ACP3_CMS::$db->escape($_POST['date_format_long']) . '\', \'' . ACP3_CMS::$db->escape($_POST['date_format_short']) . '\', \'' . $_POST['date_time_zone'] . '\', \'' . LANG . '\', \'20\', \'\')',
				'INSERT INTO `{pre}categories` VALUES (\'\', \'' . $lang->t('category_name') . '\', \'\', \'' . $lang->t('category_description') . '\', \'' . $news_mod_id[0]['id'] . '\')',
				'INSERT INTO `{pre}news` VALUES (\'\', \'' . $current_date . '\', \'' . $current_date . '\', \'' . $lang->t('news_headline') . '\', \'' . $lang->t('news_text') . '\', \'1\', \'1\', \'1\', \'\', \'\', \'\', \'\')',
				'INSERT INTO `{pre}menu_items` VALUES (\'\', 1, 1, 1, 0, 1, 2, 1, \'' . $lang->t('pages_news') . '\', \'news\', 1), (\'\', 1, 1, 2, 0, 3, 4, 1, \'' . $lang->t('pages_files') . '\', \'files\', 1), (\'\', 1, 1, 3, 0, 5, 6, 1, \'' . $lang->t('pages_gallery') . '\', \'gallery\', 1), (\'\', 1, 1, 4, 0, 7, 8, 1, \'' . $lang->t('pages_guestbook') . '\', \'guestbook\', 1), (\'\', 1, 1, 5, 0, 9, 10, 1, \'' . $lang->t('pages_polls') . '\', \'polls\', 1), (\'\', 1, 1, 6, 0, 11, 12, 1, \'' . $lang->t('pages_search') . '\', \'search\', 1), (\'\', 1, 2, 7, 0, 13, 14, 1, \'' . $lang->t('pages_contact') . '\', \'contact\', 1), (\'\', 2, 2, 8, 0, 15, 16, 1, \'' . $lang->t('pages_imprint') . '\', \'contact/imprint/\', 1)',
				'INSERT INTO `{pre}menus` (`id`, `index_name`, `title`) VALUES (1, \'main\', \'' . $lang->t('pages_main') . '\'), (2, \'sidebar\', \'' . $lang->t('pages_sidebar') . '\')',
				'INSERT INTO `{pre}seo` VALUES (\'news/details/id_1/\', \'' . $lang->t('news_headline_alias') . '\', \'\', \'\', \'1\')',
				'INSERT INTO `{pre}seo` VALUES (\'contact/imprint/\', \'' . $lang->t('pages_imprint_alias') . '\', \'\', \'\', \'1\')',
			);

			foreach ($queries as $query) {
				if (!empty($query)) {
					$query.= ';';
					$bool = ACP3_CMS::$db->query($query, 0);
					if ($bool === false) {
						$tpl->assign('install_error', true);
						break;
					}
				}
			}
		}

		// Modulkonfigurationsdateien schreiben
		$system_settings = array(
			'cache_images' => true,
			'cache_minify' => 3600,
			'date_format_long' => $_POST['date_format_long'],
			'date_format_short' => $_POST['date_format_short'],
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
			'maintenance_message' => $lang->t('offline_message'),
			'seo_aliases' => 1,
			'seo_meta_description' => '',
			'seo_meta_keywords' => '',
			'seo_mod_rewrite' => 1,
			'seo_robots' => 1,
			'seo_title' => !empty($_POST['seo_title']) ? $_POST['seo_title'] : 'ACP3',
			'version' => CONFIG_VERSION,
			'wysiwyg' => 'ckeditor'
		);
		ACP3_Config::setSettings('system', $system_settings);
		ACP3_Config::setSettings('users', array('mail' => $_POST['mail']));
		ACP3_Config::setSettings('contact', array('mail' => $_POST['mail'], 'disclaimer' => ACP3_CMS::$db->escape($lang->t('disclaimer'), 2)));
		ACP3_Config::setSettings('newsletter', array('mail' => $_POST['mail'], 'mailsig' => ACP3_CMS::$db->escape($lang->t('sincerely') . "\n\n" . $lang->t('newsletter_mailsig'))));

		$content = $tpl->fetch('result.tpl');
	}
}
if (isset($_POST['submit']) === false || isset($errors) === true && is_array($errors) === true) {
	// Zeitzonen
	$tpl->assign('time_zones', getTimeZones(date_default_timezone_get()));

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

	$tpl->assign('form', isset($_POST['submit']) ? $_POST : $defaults);

	$content = $tpl->fetch('configuration.tpl');
}