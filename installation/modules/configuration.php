<?php
if (defined('IN_INSTALL') === false)
	exit;

if (isset($_POST['submit'])) {
	$form = $_POST['form'];
	$config_path = ACP3_ROOT . 'includes/config.php';
	$db = new db();

	if (empty($form['db_host']))
		$errors[] = $lang->t('installation', 'type_in_db_host');
	if (empty($form['db_user']))
		$errors[] = $lang->t('installation', 'type_in_db_username');
	if (empty($form['db_name']))
		$errors[] = $lang->t('installation', 'type_in_db_name');
	if (!empty($form['db_host']) && !empty($form['db_user']) && !empty($form['db_name']) &&
		$db->connect($form['db_host'], $form['db_name'], $form['db_user'], $form['db_password']) !== true)
		$errors[] = $lang->t('installation', 'db_connection_failed');
	if (empty($form['user_name']))
		$errors[] = $lang->t('installation', 'type_in_user_name');
	if ((empty($form['user_pwd']) || empty($form['user_pwd_wdh'])) ||
		(!empty($form['user_pwd']) && !empty($form['user_pwd_wdh']) && $form['user_pwd'] != $form['user_pwd_wdh']))
		$errors[] = $lang->t('installation', 'type_in_pwd');
	if (validate::email($form['mail']) === false)
		$errors[] = $lang->t('common', 'wrong_email_format');
	if (validate::isNumber($form['entries']) === false)
		$errors[] = $lang->t('system', 'select_entries_per_page');
	if (validate::isNumber($form['flood']) === false)
		$errors[] = $lang->t('system', 'type_in_flood_barrier');
	if (empty($form['date_format_long']) || empty($form['date_format_short']))
		$errors[] = $lang->t('system', 'type_in_date_format');
	if (validate::isNumber($form['date_dst']) === false)
		$errors[] = $lang->t('common', 'select_daylight_saving_time');
	if (validate::isNumber($form['date_time_zone']) === false)
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
			'date_dst' => $form['date_dst'],
			'date_format_long' => $form['date_format_long'],
			'date_format_short' => $form['date_format_short'],
			'date_time_zone' => $form['date_time_zone'],
			'db_host' => $form['db_host'],
			'db_name' => $form['db_name'],
			'db_pre' => $form['db_pre'],
			'db_password' => $form['db_password'],
			'db_user' => $form['db_user'],
			'db_version' => 16,
			'design' => 'acp3',
			'entries' => $form['entries'],
			'flood' => $form['flood'],
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
			'seo_title' => !empty($form['seo_title']) ? $form['seo_title'] : 'ACP3',
			'version' => CONFIG_VERSION,
			'wysiwyg' => 'ckeditor'
		);

		// Daten in die config.php schreiben und diese laden
		writeConfigFile($config);

		$db = new db();
		$db->connect($form['db_host'], $form['db_name'], $form['db_user'], $form['db_password'], $form['db_pre']);

		$sql_file = file_get_contents(ACP3_ROOT . 'installation/modules/install.sql');
		$sql_file = str_replace(array("\r\n", "\r"), "\n", $sql_file);
		$sql_file = str_replace('{engine}', 'ENGINE=MyISAM CHARACTER SET `utf8` COLLATE `utf8_general_ci`', $sql_file);

		$sql_file_arr = explode(";\n", $sql_file);
		$salt = salt(12);
		$current_date = gmdate('U');

		$other_arr = array(
			1 => 'INSERT INTO `{pre}users` VALUES (\'\', \'' . $db->escape($form['user_name']) . '\', \'' . generateSaltedPassword($salt, $form['user_pwd']) . ':' . $salt . '\', \'0\', \':1\', \'1:1\', \':1\', \'1\', \'' . $form['mail'] . ':1\', \':1\', \':1\', \':1\', \':1\', \'' . $db->escape($form['date_format_long']) . '\', \'' . $db->escape($form['date_format_short']) . '\', \'' . $form['date_time_zone'] . '\', \'' . $form['date_dst'] .'\', \'' . LANG . '\', \'' . $form['entries'] . '\', \'\')',
			2 => 'INSERT INTO `{pre}news` VALUES (\'\', \'' . $current_date . '\', \'' . $current_date . '\', \'' . $lang->t('installation', 'news_headline') . '\', \'' . $lang->t('installation', 'news_text') . '\', \'1\', \'1\', \'1\', \'\', \'\', \'\', \'\')',
			3 => 'INSERT INTO `{pre}menu_items` VALUES (\'\', \'' . $current_date . '\', \'' . $current_date . '\', 1, 1, 1, 0, 1, 2, 1, \'' . $lang->t('installation', 'pages_news') . '\', \'news\', 1), (\'\', \'' . $current_date . '\', \'' . $current_date . '\', 1, 1, 2, 0, 3, 4, 1, \'' . $lang->t('installation', 'pages_files') . '\', \'files\', 1), (\'\', \'' . $current_date . '\', \'' . $current_date . '\', 1, 1, 3, 0, 5, 6, 1, \'' . $lang->t('installation', 'pages_gallery') . '\', \'gallery\', 1), (\'\', \'' . $current_date . '\', \'' . $current_date . '\', 1, 1, 4, 0, 7, 8, 1, \'' . $lang->t('installation', 'pages_guestbook') . '\', \'guestbook\', 1), (\'\', \'' . $current_date . '\', \'' . $current_date . '\', 1, 1, 5, 0, 9, 10, 1, \'' . $lang->t('installation', 'pages_polls') . '\', \'polls\', 1), (\'\', \'' . $current_date . '\', \'' . $current_date . '\', 1, 1, 6, 0, 11, 12, 1, \'' . $lang->t('installation', 'pages_search') . '\', \'search\', 1), (\'\', \'' . $current_date . '\', \'' . $current_date . '\', 1, 2, 7, 0, 13, 14, 1, \'' . $lang->t('installation', 'pages_contact') . '\', \'contact\', 1), (\'\', \'' . $current_date . '\', \'' . $current_date . '\', 2, 2, 8, 0, 15, 16, 1, \'' . $lang->t('installation', 'pages_imprint') . '\', \'contact/imprint/\', 1)',
			4 => 'INSERT INTO `{pre}menu_items_blocks` (`id`, `index_name`, `title`) VALUES (1, \'main\', \'' . $lang->t('installation', 'pages_main') . '\'), (2, \'sidebar\', \'' . $lang->t('installation', 'pages_sidebar') . '\')',
			5 => 'INSERT INTO `{pre}seo` VALUES (\'news/details/id_1/\', \'' . $lang->t('installation', 'news_headline_alias') . '\', \'\', \'\')',
			6 => 'INSERT INTO `{pre}seo` VALUES (\'contact/imprint/\', \'' . $lang->t('installation', 'pages_imprint_alias') . '\', \'\', \'\')',
		);
		$queries = array_merge($sql_file_arr, $other_arr);

		$data = NULL;
		$i = 0;
		foreach ($queries as $query) {
			if (!empty($query)) {
				$query.= ';';
				$data[$i]['query'] = htmlentities($query, ENT_QUOTES, 'UTF-8');
				$bool = $db->query($query, 3);
				$data[$i]['color'] = $bool !== false ? '090' : 'f00';
				$data[$i]['result'] = $bool !== false ? $lang->t('system', 'query_successfully_executed') : $lang->t('system', 'query_failed');
				++$i;
				if ($bool === false) {
					$tpl->assign('install_error', true);
					break;
				}
			}
		}
		$tpl->assign('sql_queries', $data);

		$special_resources = array(
			'comments' => array(
				'create' => 2,
			),
			'gallery' => array(
				'add_picture' => 4,
			),
			'guestbook' => array(
				'create' => 2,
			),
			'newsletter' => array(
				'compose' => 4,
				'create' => 2,
				'adm_activate' => 3,
				'sent' => 4,
			),
			'system' => array(
				'configuration' => 7,
				'designs' => 7,
				'extensions' => 7,
				'languages' => 7,
				'maintenance' => 7,
				'modules' => 7,
				'sql_export' => 7,
				'sql_import' => 7,
				'sql_optimisation' => 7,
				'update_check' => 3,
			),
			'users' => array(
				'edit_profile' => 1,
				'edit_settings' => 1,
			),
		);

		// Moduldaten in die ACL schreiben
		$modules = scandir(MODULES_DIR);
		foreach ($modules as $row) {
			if ($row !== '.' && $row !== '..' && is_dir(MODULES_DIR . $row . '/') === true) {
				$module = scandir(MODULES_DIR . $row . '/');
				$mod_id = $db->select('id', 'modules', 'name = \'' . $row . '\'');
				if (is_file(MODULES_DIR . $row . '/extensions/search.php') === true)
					$db->insert('acl_resources', array('id' => '', 'module_id' => $mod_id[0]['id'], 'page' => 'extensions/search', 'params' => '', 'privilege_id' => 1));
				if (is_file(MODULES_DIR . $row . '/extensions/feeds.php') === true)
					$db->insert('acl_resources', array('id' => '', 'module_id' => $mod_id[0]['id'], 'page' => 'extensions/feeds', 'params' => '', 'privilege_id' => 1));

				foreach ($module as $file) {
					if ($file !== '.' && $file !== '..' && is_file(MODULES_DIR . $row . '/' . $file) === true && strpos($file, '.php') !== false) {
						$file = substr($file, 0, -4);
						if (isset($special_resources[$row][$file])) {
							$privilege_id = $special_resources[$row][$file];
						} else {
							$privilege_id = 1;
							if (strpos($file, 'adm_list') === 0)
								$privilege_id = 3;
							if (strpos($file, 'create') === 0 || strpos($file, 'order') === 0)
								$privilege_id = 4;
							if (strpos($file, 'edit') === 0)
								$privilege_id = 5;
							if (strpos($file, 'delete') === 0)
								$privilege_id = 6;
							if (strpos($file, 'settings') === 0)
								$privilege_id = 7;
						}
						$db->insert('acl_resources', array('id' => '', 'module_id' => $mod_id[0]['id'], 'page' => $file, 'params' => '', 'privilege_id' => $privilege_id));
					}
				}
			}
		}

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
		config::module('contact', array('mail' => $form['mail'], 'disclaimer' => $db->escape($lang->t('installation', 'disclaimer'), 2)));
		config::module('newsletter', array('mail' => $form['mail'], 'mailsig' => $db->escape($lang->t('installation', 'sincerely') . "\n\n" . $lang->t('installation', 'newsletter_mailsig'))));
	}
}
if (!isset($_POST['submit']) || isset($errors) && is_array($errors)) {
	// Einträge pro Seite
	$entries = array();
	for ($i = 0, $j = 10; $j <= 50; $j = $j + 10, ++$i) {
		$entries[$i]['value'] = $j;
		$entries[$i]['selected'] = selectEntry('entries', $j, '20');
	}
	$tpl->assign('entries', $entries);

	// Zeitzonen
	$areas = array(-12, -11, -10, -9.5, -9, -8, -7, -6, -5, -4, -3.5, -3, -2, -1, 0, 1, 2, 3, 3.5, 4, 4.5, 5, 5.5, 5.75, 6, 6.5, 7, 8, 8.75, 9, 9.5, 10, 10.5, 11, 11.5, 12, 12.75, 13, 14);
	$check_dst = date('I');
	$offset = date('Z') - ($check_dst == '1' ? 3600 : 0);
	$time_zones = array();
	$i = 0;
	foreach ($areas as $row) {
		$time_zones[$i]['value'] = $row * 3600;
		$time_zones[$i]['selected'] = selectEntry('date_time_zone', $row * 3600, $offset);
		$time_zones[$i]['lang'] = $lang->t('common', 'utc' . $row);
		$i++;
	}
	$tpl->assign('time_zones', $time_zones);

	// Sommerzeit an/aus
	$dst = array();
	$dst[0]['value'] = '1';
	$dst[0]['checked'] = selectEntry('date_dst', '1', $check_dst, 'checked');
	$dst[0]['lang'] = $lang->t('common', 'yes');
	$dst[1]['value'] = '0';
	$dst[1]['checked'] = selectEntry('date_dst', '0', $check_dst, 'checked');
	$dst[1]['lang'] = $lang->t('common', 'no');
	$tpl->assign('dst', $dst);

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

	$tpl->assign('form', isset($form) ? $form : $defaults);
}
$content = $tpl->fetch('configuration.tpl');