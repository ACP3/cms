<?php
if (!defined('IN_INSTALL'))
	exit;

if (isset($_POST['submit'])) {
	$form = $_POST['form'];
	$config_path = ACP3_ROOT . 'includes/config.php';
	$db = new db;

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
	if (!validate::email($form['mail']))
		$errors[] = $lang->t('common', 'wrong_email_format');
	if (!validate::isNumber($form['entries']))
		$errors[] = $lang->t('system', 'select_entries_per_page');
	if (!validate::isNumber($form['flood']))
		$errors[] = $lang->t('system', 'type_in_flood_barrier');
	if (empty($form['date_format_long']) || empty($form['date_format_short']))
		$errors[] = $lang->t('system', 'type_in_date_format');
	if (!validate::isNumber($form['date_dst']))
		$errors[] = $lang->t('common', 'select_daylight_saving_time');
	if (!validate::isNumber($form['date_time_zone']))
		$errors[] = $lang->t('common', 'select_time_zone');
	if (!is_file($config_path) || !is_writable($config_path))
		$errors[] = $lang->t('installation', 'wrong_chmod_for_config_file');

	if (isset($errors)) {
		$tpl->assign('errors', $errors);
		$tpl->assign('error_msg', $tpl->fetch('error.html'));
	} else {
		// Modulkonfigurationsdateien schreiben
		config::module('contact', array('mail' => $form['mail'], 'disclaimer' => $lang->t('installation', 'disclaimer')));
		config::module('newsletter', array('mail' => $form['mail'], 'mailsig' => $lang->t('installation', 'sincerely') . "\n\n" . $lang->t('installation', 'newsletter_mailsig')));

		// Systemkonfiguration erstellen
		$config = array(
			'date_dst' => $form['date_dst'],
			'date_format_long' => db::escape($form['date_format_long']),
			'date_format_short' => db::escape($form['date_format_short']),
			'date_time_zone' => $form['date_time_zone'],
			'db_host' => $form['db_host'],
			'db_name' => $form['db_name'],
			'db_pre' => db::escape($form['db_pre']),
			'db_password' => $form['db_password'],
			'db_user' => $form['db_user'],
			'design' => 'acp3',
			'entries' => $form['entries'],
			'flood' => $form['flood'],
			'homepage' => 'news/list/',
			'lang' => LANG,
			'maintenance_mode' => 0,
			'maintenance_message' => $lang->t('installation', 'offline_message'),
			'seo_meta_description' => '',
			'seo_meta_keywords' => '',
			'seo_mod_rewrite' => 0,
			'seo_title' => !empty($form['seo_title']) ? db::escape($form['seo_title']) : 'ACP3',
			'version' => CONFIG_VERSION,
			'wysiwyg' => 'fckeditor'
		);

		// Daten in die config.php schreiben und diese laden
		config::system($config);
		require $config_path;

		$db->connect(CONFIG_DB_HOST, CONFIG_DB_NAME, CONFIG_DB_USER, CONFIG_DB_PASSWORD, CONFIG_DB_PRE);

		$sql_file = file_get_contents(ACP3_ROOT . 'installation/modules/install.sql');
		$sql_file = str_replace(array("\r\n", "\r"), "\n", $sql_file);
		$sql_file = str_replace('{pre}', $db->prefix, $sql_file);
		$sql_file = str_replace('{engine}', 'ENGINE=MyISAM CHARACTER SET `utf8` COLLATE `utf8_general_ci`', $sql_file);

		$sql_file_arr = explode(";\n", $sql_file);
		$salt = salt(12);
		$current_date = gmdate('U');

		$other_arr = array(
			1 => 'INSERT INTO `' . $db->prefix . 'users` VALUES (\'\', \'' . db::escape($form['user_name']) . '\', \'' . genSaltedPassword($salt, $form['user_pwd']) . ':' . $salt . '\', 1, \'0\', \':1\', \'1:1\', \':1\', \'1\', \'' . $form['mail'] . ':1\', \':1\', \':1\', \':1\', \':1\', \'' . db::escape($form['date_format_long']) . '\', \'' . db::escape($form['date_format_short']) . '\', \'' . CONFIG_DATE_TIME_ZONE . '\', \'' . CONFIG_DST .'\', \'' . CONFIG_LANG . '\', \'' . $form['entries'] . '\', \'\')',
			2 => 'INSERT INTO `' . $db->prefix . 'news` VALUES (\'\', \'' . $current_date . '\', \'' . $current_date . '\', \'' . $lang->t('installation', 'news_headline') . '\', \'' . $lang->t('installation', 'news_text') . '\', \'1\', \'1\', \'1\', \'\', \'\', \'\')',
			3 => 'INSERT INTO `' . $db->prefix . 'menu_items` VALUES (\'\', \'' . $current_date . '\', \'' . $current_date . '\', 1, 1, 1, 1, 2, 1, \'' . $lang->t('installation', 'pages_news') . '\', \'news\', 1), (\'\', \'' . $current_date . '\', \'' . $current_date . '\', 1, 1, 2, 3, 4, 1, \'' . $lang->t('installation', 'pages_files') . '\', \'files\', 1), (\'\', \'' . $current_date . '\', \'' . $current_date . '\', 1, 1, 3, 5, 6, 1, \'' . $lang->t('installation', 'pages_gallery') . '\', \'gallery\', 1), (\'\', \'' . $current_date . '\', \'' . $current_date . '\', 1, 1, 4, 7, 8, 1, \'' . $lang->t('installation', 'pages_guestbook') . '\', \'guestbook\', 1), (\'\', \'' . $current_date . '\', \'' . $current_date . '\', 1, 1, 5, 9, 10, 1, \'' . $lang->t('installation', 'pages_polls') . '\', \'polls\', 1), (\'\', \'' . $current_date . '\', \'' . $current_date . '\', 1, 1, 6, 11, 12, 1, \'' . $lang->t('installation', 'pages_search') . '\', \'search\', 1), (\'\', \'' . $current_date . '\', \'' . $current_date . '\', 1, 2, 7, 13, 14, 1, \'' . $lang->t('installation', 'pages_contact') . '\', \'contact\', 1), (\'\', \'' . $current_date . '\', \'' . $current_date . '\', 2, 2, 8, 15, 16, 1, \'' . $lang->t('installation', 'pages_imprint') . '\', \'contact/imprint/\', 1)',
			4 => 'INSERT INTO `' . $db->prefix . 'menu_items_blocks` (`id`, `index_name`, `title`) VALUES (1, \'main\', \'' . $lang->t('installation', 'pages_main') . '\'), (2, \'sidebar\', \'' . $lang->t('installation', 'pages_sidebar') . '\')',
		);
		$queries = array_merge($sql_file_arr, $other_arr);

		$data = NULL;
		$i = 0;
		foreach ($queries as $query) {
			if (!empty($query)) {
				$query.= ';';
				$data[$i]['query'] = $query;
				$bool = $db->query($query, 0);
				$data[$i]['color'] = $bool !== null ? '090' : 'f00';
				$data[$i]['result'] = $bool !== null ? $lang->t('system', 'query_successfully_executed') : $lang->t('system', 'query_failed');
				++$i;
				if ($bool === null) {
					$tpl->assign('install_error', true);
					break;
				}
			}
		}
		$tpl->assign('sql_queries', $data);
	}
}
if (!isset($_POST['submit']) || isset($errors) && is_array($errors)) {
	// Einträge pro Seite
	$i = 0;
	for ($j = 10; $j <= 50; $j = $j + 10) {
		$entries[$i]['value'] = $j;
		$entries[$i]['selected'] = select_entry('entries', $j, '20');
		$i++;
	}
	$tpl->assign('entries', $entries);

	// Zeitzonen
	$time_zones = array(-12, -11, -10, -9.5, -9, -8, -7, -6, -5, -4, -3.5, -3, -2, -1, 0, 1, 2, 3, 3.5, 4, 4.5, 5, 5.5, 5.75, 6, 6.5, 7, 8, 8.75, 9, 9.5, 10, 10.5, 11, 11.5, 12, 12.75, 13, 14);
	$check_dst = date('I');
	$offset = date('Z') - ($check_dst == '1' ? 3600 : 0);
	$i = 0;
	foreach ($time_zones as $row) {
		$time_zone[$i]['value'] = $row * 3600;
		$time_zone[$i]['selected'] = select_entry('date_time_zone', $row * 3600, $offset);
		$time_zone[$i]['lang'] = $lang->t('common', 'utc' . $row);
		$i++;
	}
	$tpl->assign('time_zone', $time_zone);

	// Sommerzeit an/aus
	$dst[0]['value'] = '1';
	$dst[0]['checked'] = select_entry('date_dst', '1', $check_dst, 'checked');
	$dst[0]['lang'] = $lang->t('common', 'yes');
	$dst[1]['value'] = '0';
	$dst[1]['checked'] = select_entry('date_dst', '0', $check_dst, 'checked');
	$dst[1]['lang'] = $lang->t('common', 'no');
	$tpl->assign('dst', $dst);

	$defaults['db_pre'] = 'acp3_';
	$defaults['user_name'] = 'admin';
	$defaults['flood'] = '30';
	$defaults['date_format_long'] = 'd.m.y, H:i';
	$defaults['date_format_short'] = 'd.m.y';
	$defaults['seo_title'] = 'ACP3';

	$tpl->assign('form', isset($form) ? $form : $defaults);
}
$content = $tpl->fetch('configuration.html');
?>