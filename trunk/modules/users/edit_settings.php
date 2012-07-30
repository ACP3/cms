<?php
/**
 * Users
 *
 * @author Tino Goratsch
 * @package ACP3
 * @subpackage Modules
 */

if (defined('IN_ACP3') === false)
	exit;

if ($auth->isUser() === false || ACP3_Validate::isNumber($auth->getUserId()) === false) {
	$uri->redirect('errors/403');
} else {
	$settings = ACP3_Config::getModuleSettings('users');

	$breadcrumb->append($lang->t('users', 'users'), $uri->route('users'))
			   ->append($lang->t('users', 'home'), $uri->route('users/home'))
			   ->append($lang->t('users', 'edit_settings'));

	if (isset($_POST['submit']) === true) {
		if ($settings['language_override'] == 1 && $lang->languagePackExists($_POST['language']) === false)
			$errors['language'] = $lang->t('users', 'select_language');
		if ($settings['entries_override'] == 1 && ACP3_Validate::isNumber($_POST['entries']) === false)
			$errors['entries'] = $lang->t('common', 'select_records_per_page');
		if (empty($_POST['date_format_long']) || empty($_POST['date_format_short']))
			$errors[] = $lang->t('system', 'type_in_date_format');
		if (ACP3_Validate::timeZone($_POST['date_time_zone']) === false)
			$errors['time-zone'] = $lang->t('common', 'select_time_zone');

		if (isset($errors) === true) {
			$tpl->assign('error_msg', errorBox($errors));
		} elseif (ACP3_Validate::formToken() === false) {
			ACP3_View::setContent(errorBox($lang->t('common', 'form_already_submitted')));
		} else {
			$update_values = array(
				'date_format_long' => $db->escape($_POST['date_format_long']),
				'date_format_short' => $db->escape($_POST['date_format_short']),
				'time_zone' => $_POST['date_time_zone'],
			);
			if ($settings['language_override'] == 1)
				$update_values['language'] = $_POST['language'];
			if ($settings['entries_override'] == 1)
				$update_values['entries'] = (int) $_POST['entries'];

			$bool = $db->update('users', $update_values, 'id = \'' . $auth->getUserId() . '\'');

			$session->unsetFormToken();

			setRedirectMessage($bool !== false ? $lang->t('common', 'settings_success') : $lang->t('common', 'settings_error'), 'users/home');
		}
	}
	if (isset($_POST['submit']) === false || isset($errors) === true && is_array($errors) === true) {
		$user = $db->select('date_format_long, date_format_short, time_zone, language, entries', 'users', 'id = \'' . $auth->getUserId() . '\'');

		$tpl->assign('language_override', $settings['language_override']);
		$tpl->assign('entries_override', $settings['entries_override']);

		// Sprache
		$languages = array();
		$lang_dir = scandir(ACP3_ROOT . 'languages');
		$c_lang_dir = count($lang_dir);
		for ($i = 0; $i < $c_lang_dir; ++$i) {
			$lang_info = ACP3_XML::parseXmlFile(ACP3_ROOT . 'languages/' . $lang_dir[$i] . '/info.xml', '/language');
			if (!empty($lang_info)) {
				$name = $lang_info['name'];
				$languages[$name]['dir'] = $lang_dir[$i];
				$languages[$name]['selected'] = selectEntry('language', $lang_dir[$i], $db->escape($user[0]['language'], 3));
				$languages[$name]['name'] = $lang_info['name'];
			}
		}
		ksort($languages);
		$tpl->assign('languages', $languages);

		// Einträge pro Seite
		$tpl->assign('entries', recordsPerPage((int) $user[0]['entries']));

		// Zeitzonen
		$tpl->assign('time_zones', $date->getTimeZones($user[0]['time_zone']));

		$user[0]['date_format_long'] = $db->escape($user[0]['date_format_long'], 3);
		$user[0]['date_format_short'] = $db->escape($user[0]['date_format_short'], 3);

		$tpl->assign('form', isset($_POST['submit']) ? $_POST : $user[0]);

		$session->generateFormToken();

		ACP3_View::setContent(ACP3_View::fetchTemplate('users/edit_settings.tpl'));
	}
}