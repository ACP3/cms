<?php
if (!defined('IN_INSTALL'))
	exit;

require_once '../includes/classes/validate.php';
$validate = new validate;

$form = $_POST['form'];

if (empty($form['db_host']))
	$errors[] = lang('type_in_db_host');
if (empty($form['db_user']))
	$errors[] = lang('type_in_db_user_name');
if (empty($form['db_name']))
	$errors[] = lang('type_in_db_name');
if (empty($form['db_type']))
	$errors[] = lang('select_db_type');
if (!empty($form['db_host']) && !empty($form['db_user']) && !empty($form['db_name'])) {
	if ($form['db_type'] == 'mysql') {
		$db = @mysql_connect($form['db_host'], $form['db_user'], $form['db_pwd']);
		$db_select = @mysql_select_db($form['db_name'], $db);
		if (!$db || !$db_select)
			$errors[] = lang('db_connection_failed');
	} elseif ($form['db_type'] == 'mysqli') {
		$db = @mysqli_connect($form['db_host'], $form['db_user'], $form['db_pwd'], $form['db_name']);
		if (mysqli_connect_errno())
			$errors[] = lang('db_connection_failed');
	}
}
if (empty($form['user_name']))
	$errors[] = lang('type_in_user_name');
if ((empty($form['user_pwd']) || empty($form['user_pwd_wdh'])) || (!empty($form['user_pwd']) && !empty($form['user_pwd_wdh']) && $form['user_pwd'] != $form['user_pwd_wdh']))
	$errors[] = lang('type_in_pwd');
if (!$validate->email($form['mail']))
	$errors[] = lang('wrong_email_format');
if (!$validate->is_number($form['entries']))
	$errors[] = lang('select_entries_per_page');
if (!$validate->is_number($form['flood']))
	$errors[] = lang('type_in_flood_barrier');
if (!$validate->is_number($form['sef']))
	$errors[] = lang('select_sef_uris');
if (empty($form['date']))
	$errors[] = lang('type_in_date_format');
if (!$validate->is_number($form['dst']))
	$errors[] = lang('select_daylight_saving_time');
if (!$validate->is_number($form['time_zone']))
	$errors[] = lang('select_time_zone');
if (empty($form['title']))
	$errors[] = lang('type_in_title');
if (!is_file('../includes/config.php') || !is_writable('../includes/config.php'))
	$errors[] = lang('wrong_chmod_for_config_file');

if (isset($errors)) {
	$tpl->assign('errors', $errors);
	$tpl->assign('error_msg', $tpl->fetch('error.html'));
} else {
	$form['date'] = mask($form['date']);
	$form['design'] = 'acp3';
	$form['lang'] = is_file('../languages/' . LANG . '/info.php') ? LANG : 'de';
	$form['maintenance'] = '0';
	$form['maintenance_msg'] = lang('offline_message');
	$form['meta_description'] = mask($form['meta_description']);
	$form['meta_keywords'] = '';
	$form['title'] = mask($form['title']);
	$form['version'] = '4.0b8';
	ksort($form);

	// Modulkonfigurationsdateien schreiben
	write_config('contact', array('mail' => $form['mail'], 'address' => '', 'telephone' => '', 'fax' => '', 'disclaimer' => lang('disclaimer'), 'miscellaneous' => ''));
	write_config('newsletter', array('mail' => $form['mail'], 'mailsig' => lang('sincerely') . "\n\n" . lang('newsletter_mailsig')));

	$config_file = '<?php' . "\n";
	$config_file.= 'define(\'INSTALLED\', true);' . "\n";
	foreach ($form as $key => $value) {
		if ($key != 'mail' && $key != 'user_name' && $key != 'user_pwd' && $key != 'user_pwd_wdh') {
			$config_file.= 'define(\'CONFIG_' . strtoupper($key) . '\', \'' . $value . '\');' . "\n";
		}
	}
	$config_file.= '?>';

	$config_path = '../includes/config.php';
	@file_put_contents($config_path, $config_file);

	require $config_path;
	require '../includes/classes/db.php';

	$db = new db();

	$sql_file = file_get_contents('modules/install/install.sql');
	$sql_file = str_replace(array("\r\n", "\r"), "\n", $sql_file);
	$sql_file = str_replace('{pre}', CONFIG_DB_PRE, $sql_file);
	if (version_compare(mysql_get_client_info(), '4.1', '>=')) {
		$sql_file = str_replace('{engine}', 'ENGINE=MyISAM CHARACTER SET `utf8` COLLATE `utf8_general_ci`', $sql_file);
	} else {
		$sql_file = str_replace('{engine}', 'TYPE=MyISAM CHARSET=utf-8', $sql_file);
	}

	$sql_file_arr = explode("\n", $sql_file);
	$salt = salt(12);
	$current_date = gmdate('U');

	$other_arr = array(
		1 => 'INSERT INTO `' . CONFIG_DB_PRE . 'users` VALUES (1, \'' . mask($form['user_name']) . '\', \'\', \'' . sha1($salt . sha1($form['user_pwd'])) . ':' . $salt . '\', 1, \'' . $form['mail'] . '\', \'\', \'' . CONFIG_TIME_ZONE . '\', \'' . CONFIG_DST .'\', \'' . CONFIG_LANG . '\', \'\');',
		2 => 'INSERT INTO `' . CONFIG_DB_PRE . 'news` VALUES (\'\', \'' . $current_date . '\', \'' . $current_date . '\', \'' . lang('news_headline') . '\', \'' . lang('news_text') . '\', \'1\', \'\', \'\', \'\');',
		3 => 'INSERT INTO `' . CONFIG_DB_PRE . 'pages` VALUES (\'\', \'' . $current_date . '\', \'' . $current_date . '\', 2, 0, 1, 0, \'' . lang('pages_news') . '\', \'news/list\', 1, \'\'), (\'\', \'' . $current_date . '\', \'' . $current_date . '\', 2, 0, 1, 1, \'' . lang('pages_files') . '\', \'files/list\', 1, \'\'), (\'\', \'' . $current_date . '\', \'' . $current_date . '\', 2, 0, 1, 2, \'' . lang('pages_gallery') . '\', \'gallery/list\', 1, \'\'), (\'\', \'' . $current_date . '\', \'' . $current_date . '\', 2, 0, 1, 3, \'' . lang('pages_guestbook') . '\', \'guestbook/list\', 1, \'\'), (\'\', \'' . $current_date . '\', \'' . $current_date . '\', 2, 0, 1, 4, \'' . lang('pages_polls') . '\', \'polls/list\', 1, \'\'), (\'\', \'' . $current_date . '\', \'' . $current_date . '\', 2, 0, 1, 5, \'' . lang('pages_search') . '\', \'search/list\', 1, \'\'), (\'\', \'' . $current_date . '\', \'' . $current_date . '\', 2, 0, 2, 0, \'' . lang('pages_contact') . '\', \'contact/list\', 1, \'\'), (\'\', \'' . $current_date . '\', \'' . $current_date . '\', 2, 0, 2, 1, \'' . lang('pages_imprint') . '\', \'contact/imprint\', 1, \'\');',
		4 => 'INSERT INTO `' . CONFIG_DB_PRE . 'pages_blocks` (`id`, `index_name`, `title`) VALUES (1, \'main\', \'' . lang('pages_main') . '\'), (2, \'sidebar\', \'' . lang('pages_sidebar') . '\');',
	);
	$new_arr = array_merge($sql_file_arr, $other_arr);

	$data = NULL;
	$i = 0;
	foreach ($new_arr as $query) {
		if (!empty($query)) {
			$data[$i]['query'] = $query;
			$bool = $db->query($query, 3);
			$data[$i]['color'] = $bool ? '090' : 'f00';
			$data[$i]['result'] = $bool ? lang('query_successfully_executed') : lang('query_failed');
			$i++;
			if (!$bool) {
				$tpl->assign('install_error', true);
				break;
			}
		}
	}
	$tpl->assign('sql_queries', $data);
}
?>
