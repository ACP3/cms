<?php
/**
 * Users
 *
 * @author Goratsch Webdesign
 * @package ACP3
 * @subpackage Modules
 */

if (!defined('IN_ACP3'))
	exit;

if (!$auth->is_user() || !preg_match('/\d/', $_SESSION['acp3_id'])) {
	redirect('errors/403');
} else {
	$breadcrumb->assign(lang('users', 'users'), uri('users'));
	$breadcrumb->assign(lang('users', 'home'), uri('users/home'));
	$breadcrumb->assign(lang('users', 'edit_settings'));

	if (isset($_POST['submit'])) {
		include 'modules/users/entry.php';
	}
	if (!isset($_POST['submit']) || isset($errors) && is_array($errors)) {
		$user = $db->select('time_zone, dst, language', 'users', 'id = \'' . $_SESSION['acp3_id'] . '\'');

		// Zeitzonen
		$time_zones = array(-12, -11, -10, -9.5, -9, -8, -7, -6, -5, -4, -3.5, -3, -2, -1, 0, 1, 2, 3, 3.5, 4, 4.5, 5, 5.5, 5.75, 6, 6.5, 7, 8, 8.75, 9, 9.5, 10, 10.5, 11, 11.5, 12, 12.75, 13, 14);
		$i = 0;
		foreach ($time_zones as $row) {
			$time_zone[$i]['value'] = $row * 3600;
			$time_zone[$i]['selected'] = select_entry('time_zone', $row * 3600, $user[0]['time_zone']);
			$time_zone[$i]['lang'] = lang('common', 'utc' . $row);
			$i++;
		}
		$tpl->assign('time_zone', $time_zone);

		// Sommerzeit an/aus
		$dst[0]['checked'] = select_entry('dst', '1', $user[0]['dst'], 'checked');
		$dst[1]['checked'] = select_entry('dst', '0', $user[0]['dst'], 'checked');
		$tpl->assign('dst', $dst);

		// Sprache
		$languages = array();
		$lang_dir = scandir('languages');
		$c_lang_dir = count($lang_dir);
		for ($i = 0; $i < $c_lang_dir; $i++) {
			$lang_info = array();
			if ($lang_dir[$i] != '.' && $lang_dir[$i] != '..' && is_file('languages/' . $lang_dir[$i] . '/info.php')) {
				include 'languages/' . $lang_dir[$i] . '/info.php';
				$name = $lang_info['name'];
				$languages[$name]['dir'] = $lang_dir[$i];
				$languages[$name]['selected'] = select_entry('language', $lang_dir[$i], $db->escape($user[0]['language'], 3));
				$languages[$name]['name'] = $lang_info['name'];
			}
		}
		ksort($languages);
		$tpl->assign('languages', $languages);

		$content = $tpl->fetch('users/edit_settings.html');
	}
}
?>