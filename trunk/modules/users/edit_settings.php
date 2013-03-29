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

if (ACP3_CMS::$auth->isUser() === false || ACP3_Validate::isNumber(ACP3_CMS::$auth->getUserId()) === false) {
	ACP3_CMS::$uri->redirect('errors/403');
} else {
	$settings = ACP3_Config::getSettings('users');

	ACP3_CMS::$breadcrumb
	->append(ACP3_CMS::$lang->t('users', 'users'), ACP3_CMS::$uri->route('users'))
	->append(ACP3_CMS::$lang->t('users', 'home'), ACP3_CMS::$uri->route('users/home'))
	->append(ACP3_CMS::$lang->t('users', 'edit_settings'));

	if (isset($_POST['submit']) === true) {
		if ($settings['language_override'] == 1 && ACP3_CMS::$lang->languagePackExists($_POST['language']) === false)
			$errors['language'] = ACP3_CMS::$lang->t('users', 'select_language');
		if ($settings['entries_override'] == 1 && ACP3_Validate::isNumber($_POST['entries']) === false)
			$errors['entries'] = ACP3_CMS::$lang->t('system', 'select_records_per_page');
		if (empty($_POST['date_format_long']) || empty($_POST['date_format_short']))
			$errors[] = ACP3_CMS::$lang->t('system', 'type_in_date_format');
		if (ACP3_Validate::timeZone($_POST['date_time_zone']) === false)
			$errors['time-zone'] = ACP3_CMS::$lang->t('system', 'select_time_zone');
		if (in_array($_POST['mail_display'], array(0, 1)) === false)
			$errors[] = ACP3_CMS::$lang->t('users', 'select_mail_display');
		if (in_array($_POST['address_display'], array(0, 1)) === false)
			$errors[] = ACP3_CMS::$lang->t('users', 'select_address_display');
		if (in_array($_POST['country_display'], array(0, 1)) === false)
			$errors[] = ACP3_CMS::$lang->t('users', 'select_country_display');
		if (in_array($_POST['birthday_display'], array(0, 1, 2)) === false)
			$errors[] = ACP3_CMS::$lang->t('users', 'select_birthday_display');

		if (isset($errors) === true) {
			ACP3_CMS::$view->assign('error_msg', errorBox($errors));
		} elseif (ACP3_Validate::formToken() === false) {
			ACP3_CMS::setContent(errorBox(ACP3_CMS::$lang->t('system', 'form_already_submitted')));
		} else {
			$update_values = array(
				'mail_display' => (int) $_POST['mail_display'],
				'birthday_display' => (int) $_POST['birthday_display'],
				'address_display' => (int) $_POST['address_display'],
				'country_display' => (int) $_POST['country_display'],
				'date_format_long' => str_encode($_POST['date_format_long']),
				'date_format_short' => str_encode($_POST['date_format_short']),
				'time_zone' => $_POST['date_time_zone'],
			);
			if ($settings['language_override'] == 1)
				$update_values['language'] = $_POST['language'];
			if ($settings['entries_override'] == 1)
				$update_values['entries'] = (int) $_POST['entries'];

			$bool = ACP3_CMS::$db2->update(DB_PRE . 'users', $update_values, array('id' => ACP3_CMS::$auth->getUserId()));

			ACP3_CMS::$session->unsetFormToken();

			setRedirectMessage($bool, ACP3_CMS::$lang->t('system', $bool !== false ? 'settings_success' : 'settings_error'), 'users/home');
		}
	}
	if (isset($_POST['submit']) === false || isset($errors) === true && is_array($errors) === true) {
		$user = ACP3_CMS::$db2->fetchAssoc('SELECT mail_display, birthday_display, address_display, country_display, date_format_long, date_format_short, time_zone, language, entries FROM ' . DB_PRE . 'users WHERE id = ?', array(ACP3_CMS::$auth->getUserId()));

		ACP3_CMS::$view->assign('language_override', $settings['language_override']);
		ACP3_CMS::$view->assign('entries_override', $settings['entries_override']);

		// Sprache
		$languages = array();
		$lang_dir = scandir(ACP3_ROOT . 'languages');
		$c_lang_dir = count($lang_dir);
		for ($i = 0; $i < $c_lang_dir; ++$i) {
			$lang_info = ACP3_XML::parseXmlFile(ACP3_ROOT . 'languages/' . $lang_dir[$i] . '/info.xml', '/language');
			if (!empty($lang_info)) {
				$name = $lang_info['name'];
				$languages[$name]['dir'] = $lang_dir[$i];
				$languages[$name]['selected'] = selectEntry('language', $lang_dir[$i], $user['language']);
				$languages[$name]['name'] = $lang_info['name'];
			}
		}
		ksort($languages);
		ACP3_CMS::$view->assign('languages', $languages);

		// Einträge pro Seite
		ACP3_CMS::$view->assign('entries', recordsPerPage((int) $user['entries']));

		// Zeitzonen
		ACP3_CMS::$view->assign('time_zones', ACP3_CMS::$date->getTimeZones($user['time_zone']));

		$mail_display = array();
		$mail_display[0]['value'] = '1';
		$mail_display[0]['checked'] = selectEntry('mail_display', '1', $user['mail_display'], 'checked');
		$mail_display[0]['lang'] = ACP3_CMS::$lang->t('system', 'yes');
		$mail_display[1]['value'] = '0';
		$mail_display[1]['checked'] = selectEntry('mail_display', '0', $user['mail_display'], 'checked');
		$mail_display[1]['lang'] = ACP3_CMS::$lang->t('system', 'no');
		ACP3_CMS::$view->assign('mail_display', $mail_display);

		$address_display = array();
		$address_display[0]['value'] = '1';
		$address_display[0]['checked'] = selectEntry('address_display', '1', $user['address_display'], 'checked');
		$address_display[0]['lang'] = ACP3_CMS::$lang->t('system', 'yes');
		$address_display[1]['value'] = '0';
		$address_display[1]['checked'] = selectEntry('address_display', '0', $user['address_display'], 'checked');
		$address_display[1]['lang'] = ACP3_CMS::$lang->t('system', 'no');
		ACP3_CMS::$view->assign('address_display', $address_display);

		$country_display = array();
		$country_display[0]['value'] = '1';
		$country_display[0]['checked'] = selectEntry('country_display', '1', $user['country_display'], 'checked');
		$country_display[0]['lang'] = ACP3_CMS::$lang->t('system', 'yes');
		$country_display[1]['value'] = '0';
		$country_display[1]['checked'] = selectEntry('country_display', '0', $user['country_display'], 'checked');
		$country_display[1]['lang'] = ACP3_CMS::$lang->t('system', 'no');
		ACP3_CMS::$view->assign('country_display', $country_display);

		$birthday_display = array();
		$birthday_display[0]['name'] = 'hide';
		$birthday_display[0]['value'] = '0';
		$birthday_display[0]['checked'] = selectEntry('birthday_display', '0', $user['birthday_display'], 'checked');
		$birthday_display[0]['lang'] = ACP3_CMS::$lang->t('users', 'birthday_hide');
		$birthday_display[1]['name'] = 'full';
		$birthday_display[1]['value'] = '1';
		$birthday_display[1]['checked'] = selectEntry('birthday_display', '1', $user['birthday_display'], 'checked');
		$birthday_display[1]['lang'] = ACP3_CMS::$lang->t('users', 'birthday_display_completely');
		$birthday_display[2]['name'] = 'hide_year';
		$birthday_display[2]['value'] = '2';
		$birthday_display[2]['checked'] = selectEntry('birthday_display', '2', $user['birthday_display'], 'checked');
		$birthday_display[2]['lang'] = ACP3_CMS::$lang->t('users', 'birthday_hide_year');
		ACP3_CMS::$view->assign('birthday_display', $birthday_display);

		ACP3_CMS::$view->assign('form', isset($_POST['submit']) ? $_POST : $user);

		ACP3_CMS::$session->generateFormToken();

		ACP3_CMS::setContent(ACP3_CMS::$view->fetchTemplate('users/edit_settings.tpl'));
	}
}