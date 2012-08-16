<?php
if (defined('IN_INSTALL') === false)
	exit;

if (isset($_POST['submit'])) {
	$config_path = ACP3_ROOT . 'includes/config.php';
	$db = new ACP3_DB();

	if (empty($_POST['db_host']))
		$errors[] = $lang->t('installation', 'type_in_db_host');
	if (empty($_POST['db_user']))
		$errors[] = $lang->t('installation', 'type_in_db_username');
	if (empty($_POST['db_name']))
		$errors[] = $lang->t('installation', 'type_in_db_name');
	if (!empty($_POST['db_host']) && !empty($_POST['db_user']) && !empty($_POST['db_name']) &&
		$db->connect($_POST['db_host'], $_POST['db_name'], $_POST['db_user'], $_POST['db_password']) !== true)
		$errors[] = $lang->t('installation', 'db_connection_failed');
	if (empty($_POST['user_name']))
		$errors[] = $lang->t('installation', 'type_in_user_name');
	if ((empty($_POST['user_pwd']) || empty($_POST['user_pwd_wdh'])) ||
		(!empty($_POST['user_pwd']) && !empty($_POST['user_pwd_wdh']) && $_POST['user_pwd'] != $_POST['user_pwd_wdh']))
		$errors[] = $lang->t('installation', 'type_in_pwd');
	if (ACP3_Validate::email($_POST['mail']) === false)
		$errors[] = $lang->t('common', 'wrong_email_format');
	if (ACP3_Validate::isNumber($_POST['entries']) === false)
		$errors[] = $lang->t('common', 'select_records_per_page');
	if (ACP3_Validate::isNumber($_POST['flood']) === false)
		$errors[] = $lang->t('system', 'type_in_flood_barrier');
	if (empty($_POST['date_format_long']) || empty($_POST['date_format_short']))
		$errors[] = $lang->t('system', 'type_in_date_format');
	if (ACP3_Validate::timeZone($_POST['date_time_zone']) === false)
		$errors[] = $lang->t('common', 'select_time_zone');
	if (is_file($config_path) === false || is_writable($config_path) === false)
		$errors[] = $lang->t('installation', 'wrong_chmod_for_config_file');

	if (isset($errors)) {
		$tpl->assign('errors', $errors);
		$tpl->assign('error_msg', $tpl->fetch('error_box.tpl'));
	} else {
		// Systemkonfiguration erstellen
		$config = array(
			'cache_images' => true,
			'cache_minify' => 3600,
			'date_format_long' => $_POST['date_format_long'],
			'date_format_short' => $_POST['date_format_short'],
			'date_time_zone' => $_POST['date_time_zone'],
			'db_host' => $_POST['db_host'],
			'db_name' => $_POST['db_name'],
			'db_pre' => $_POST['db_pre'],
			'db_password' => $_POST['db_password'],
			'db_user' => $_POST['db_user'],
			'db_version' => 25,
			'design' => 'acp3',
			'entries' => $_POST['entries'],
			'flood' => $_POST['flood'],
			'homepage' => 'news/list/',
			'lang' => LANG,
			'mailer_smtp_auth' => false,
			'mailer_smtp_host' => '',
			'mailer_smtp_password' => '',
			'mailer_smtp_port' => 25,
			'mailer_smtp_security' => '',
			'mailer_smtp_user' => '',
			'mailer_type' => 'mail',
			'maintenance_mode' => false,
			'maintenance_message' => $lang->t('installation', 'offline_message'),
			'seo_aliases' => true,
			'seo_meta_description' => '',
			'seo_meta_keywords' => '',
			'seo_mod_rewrite' => false,
			'seo_robots' => 1,
			'seo_title' => !empty($_POST['seo_title']) ? $_POST['seo_title'] : 'ACP3',
			'version' => CONFIG_VERSION,
			'wysiwyg' => 'ckeditor'
		);

		// Daten in die config.php schreiben und diese laden
		writeConfigFile($config);

		$db = new ACP3_DB();
		$db->connect($_POST['db_host'], $_POST['db_name'], $_POST['db_user'], $_POST['db_password'], $_POST['db_pre']);

		$sql_file = file_get_contents(ACP3_ROOT . 'installation/modules/install.sql');
		$sql_file = str_replace(array("\r\n", "\r"), "\n", $sql_file);
		$sql_file = str_replace('{engine}', 'ENGINE=MyISAM CHARACTER SET `utf8` COLLATE `utf8_general_ci`', $sql_file);

		$sql_file_arr = explode(";\n", $sql_file);
		$salt = salt(12);
		$current_date = gmdate('Y-m-d H:i:s');

		$other_arr = array(
			1 => 'INSERT INTO `{pre}users` VALUES (\'\', 1, \'' . $db->escape($_POST['user_name']) . '\', \'' . generateSaltedPassword($salt, $_POST['user_pwd']) . ':' . $salt . '\', \'0\', \':1\', \'1:1\', \':1\', \'1\', \'' . $_POST['mail'] . ':1\', \':1\', \':1\', \':1\', \':1\', \'' . $db->escape($_POST['date_format_long']) . '\', \'' . $db->escape($_POST['date_format_short']) . '\', \'' . $_POST['date_time_zone'] . '\', \'' . LANG . '\', \'' . $_POST['entries'] . '\', \'\')',
			2 => 'INSERT INTO `{pre}news` VALUES (\'\', \'' . $current_date . '\', \'' . $current_date . '\', \'' . $lang->t('installation', 'news_headline') . '\', \'' . $lang->t('installation', 'news_text') . '\', \'1\', \'1\', \'1\', \'\', \'\', \'\', \'\')',
			3 => 'INSERT INTO `{pre}menu_items` VALUES (\'\', 1, 1, 1, 0, 1, 2, 1, \'' . $lang->t('installation', 'pages_news') . '\', \'news\', 1), (\'\', 1, 1, 2, 0, 3, 4, 1, \'' . $lang->t('installation', 'pages_files') . '\', \'files\', 1), (\'\', 1, 1, 3, 0, 5, 6, 1, \'' . $lang->t('installation', 'pages_gallery') . '\', \'gallery\', 1), (\'\', 1, 1, 4, 0, 7, 8, 1, \'' . $lang->t('installation', 'pages_guestbook') . '\', \'guestbook\', 1), (\'\', 1, 1, 5, 0, 9, 10, 1, \'' . $lang->t('installation', 'pages_polls') . '\', \'polls\', 1), (\'\', 1, 1, 6, 0, 11, 12, 1, \'' . $lang->t('installation', 'pages_search') . '\', \'search\', 1), (\'\', 1, 2, 7, 0, 13, 14, 1, \'' . $lang->t('installation', 'pages_contact') . '\', \'contact\', 1), (\'\', 2, 2, 8, 0, 15, 16, 1, \'' . $lang->t('installation', 'pages_imprint') . '\', \'contact/imprint/\', 1)',
			4 => 'INSERT INTO `{pre}menu_items_blocks` (`id`, `index_name`, `title`) VALUES (1, \'main\', \'' . $lang->t('installation', 'pages_main') . '\'), (2, \'sidebar\', \'' . $lang->t('installation', 'pages_sidebar') . '\')',
			5 => 'INSERT INTO `{pre}seo` VALUES (\'news/details/id_1/\', \'' . $lang->t('installation', 'news_headline_alias') . '\', \'\', \'\', \'1\')',
			6 => 'INSERT INTO `{pre}seo` VALUES (\'contact/imprint/\', \'' . $lang->t('installation', 'pages_imprint_alias') . '\', \'\', \'\', \'1\')',
		);
		$queries = array_merge($sql_file_arr, $other_arr);

		$data = NULL;
		$i = 0;
		foreach ($queries as $query) {
			if (!empty($query)) {
				$query.= ';';
				$data[$i]['query'] = htmlentities($query, ENT_QUOTES, 'UTF-8');
				$bool = $db->query($query, 3);
				$data[$i]['class'] = $bool !== false ? 'alert-success' : 'alert-error';
				$data[$i]['result'] = $bool !== false ? $lang->t('system', 'query_successfully_executed') : $lang->t('system', 'query_failed');
				++$i;
				if ($bool === false) {
					$tpl->assign('install_error', true);
					break;
				}
			}
		}
		$tpl->assign('sql_queries', $data);

		ACP3_ACL::resetResources();

		$roles = $db->select('id', 'acl_roles');
		$modules = $db->select('id', 'modules');
		$privileges = $db->select('id', 'acl_privileges');

		foreach ($roles as $role) {
			foreach ($modules as $module) {
				foreach ($privileges as $privilege) {
					$permission = 0;
					if ($role['id'] == 1 && ($privilege['id'] == 1 || $privilege['id'] == 2))
						$permission = 1;
					if ($role['id'] > 1 && $role['id'] < 4)
						$permission = 2;
					if ($role['id'] == 3 && $privilege['id'] == 3)
						$permission = 1;
					if ($role['id'] == 4)
						$permission = 1;

					$db->insert('acl_rules', array('id' => '', 'role_id' => $role['id'], 'module_id' => $module['id'], 'privilege_id' => $privilege['id'], 'permission' => $permission));
				}
			}
		}

		// Modulkonfigurationsdateien schreiben
		ACP3_Config::module('contact', array('mail' => $_POST['mail'], 'disclaimer' => $db->escape($lang->t('installation', 'disclaimer'), 2)));
		ACP3_Config::module('newsletter', array('mail' => $_POST['mail'], 'mailsig' => $db->escape($lang->t('installation', 'sincerely') . "\n\n" . $lang->t('installation', 'newsletter_mailsig'))));
	}
}
if (isset($_POST['submit']) === false || isset($errors) === true && is_array($errors) === true) {
	// Einträge pro Seite
	$tpl->assign('entries', recordsPerPage(20));

	// Zeitzonen
	$tpl->assign('time_zones', getTimeZones(date_default_timezone_get()));

	$defaults = array(
		'db_host' => 'localhost',
		'db_pre' => 'acp3_',
		'db_user' => '',
		'db_name' => '',
		'user_name' => 'admin',
		'mail' => '',
		'flood' => '30',
		'date_format_long' => 'd.m.y, H:i',
		'date_format_short' => 'd.m.y',
		'seo_title' => 'ACP3',
	);

	$tpl->assign('form', isset($_POST['submit']) ? $_POST : $defaults);
}
$content = $tpl->fetch('configuration.tpl');