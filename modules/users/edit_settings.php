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
		$form = $_POST['form'];

		if (!is_numeric($form['time_zone']))
			$errors[] = lang('common', 'select_time_zone');
		if (!$validate->is_number($form['dst']))
			$errors[] = lang('common', 'select_daylight_saving_time');
		if (!is_file('languages/' . $db->escape($form['language'], 2) . '/info.php'))
			$errors[] = lang('users', 'select_language');

		if (isset($errors)) {
			combo_box($errors);
		} else {
			$update_values = array(
				'time_zone' => $form['time_zone'],
				'dst' => $form['dst'],
				'language' => $db->escape($form['language'], 2),
			);

			$bool = $db->update('users', $update_values, 'id = \'' . $_SESSION['acp3_id'] . '\'');

			$content = combo_box($bool ? lang('users', 'edit_settings_success') : lang('users', 'edit_settings_error'), uri('users/home'));
		}
	}
	if (!isset($_POST['submit']) || isset($errors) && is_array($errors)) {
		$user = $db->select('time_zone, dst, language', 'users', 'id = \'' . $_SESSION['acp3_id'] . '\'');

		// Zeitzonen
		$tpl->assign('time_zone', time_zones($user[0]['time_zone']));

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