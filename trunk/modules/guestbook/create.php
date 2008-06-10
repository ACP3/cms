<?php
/**
 * Guestbook
 *
 * @author Goratsch Webdesign
 * @package ACP3
 * @subpackage Modules
 */

if (!defined('IN_ACP3'))
	exit;

breadcrumb::assign($lang->t('guestbook', 'guestbook'), uri('guestbook'));
breadcrumb::assign($lang->t('guestbook', 'create'));

if (isset($_POST['submit'])) {
	$ip = $_SERVER['REMOTE_ADDR'];
	$form = $_POST['form'];

	// Flood Sperre
	$flood = $db->select('date', 'guestbook', 'ip = \'' . $ip . '\'', 'id DESC', '1');
	if (count($flood) == '1') {
		$flood_time = $flood[0]['date'] + CONFIG_FLOOD;
	}
	$time = dateAligned(2, time());

	if (isset($flood_time) && $flood_time > $time)
		$errors[] = sprintf($lang->t('common', 'flood_no_entry_possible'), $flood_time - $time);
	if (empty($form['name']))
		$errors[] = $lang->t('common', 'name_to_short');
	if (!empty($form['mail']) && !validate::email($form['mail']))
		$errors[] = $lang->t('common', 'wrong_email_format');
	if (strlen($form['message']) < 3)
		$errors[] = $lang->t('common', 'message_to_short');
	if (!validate::captcha($form['captcha'], $form['hash']))
		$errors[] = $lang->t('captcha', 'invalid_captcha_entered');

	if (isset($errors)) {
		$tpl->assign('error_msg', comboBox($errors));
	} else {
		$insert_values = array(
			'id' => '',
			'ip' => $ip,
			'date' => $time,
			'name' => $db->escape($form['name']),
			'user_id' => $auth->isUser() ? USER_ID : '',
			'message' => $db->escape($form['message']),
			'website' => $db->escape($form['website'], 2),
			'mail' => $form['mail'],
		);

		$bool = $db->insert('guestbook', $insert_values);

		$content = comboBox($bool ? $lang->t('guestbook', 'create_success') : $lang->t('guestbook', 'create_error'), uri('guestbook'));
	}
}
if (!isset($_POST['submit']) || isset($errors) && is_array($errors)) {
	// Emoticons einbinden
	if (modules::check('emoticons', 'functions')) {
		include_once ACP3_ROOT . 'modules/emoticons/functions.php';
		$tpl->assign('emoticons', emoticonsList());
	}
	// Falls Benutzer eingeloggt ist, Formular schon teilweise ausfüllen
	if ($auth->isUser()) {
		$user = $auth->getUserInfo();
		$disabled = ' readonly="readonly" class="readonly"';

		if (isset($form)) {
			$form['name'] = $user['nickname'];
			$form['name_disabled'] = $disabled;
			$form['mail'] = $user['mail'];
			$form['mail_disabled'] = $disabled;
			$form['website_disabled'] = !empty($user['website']) ? $disabled : '';
		} else {
			$user['name'] = $user['nickname'];
			$user['name_disabled'] = $disabled;
			$user['mail_disabled'] = $disabled;
			$user['website_disabled'] = !empty($user['website']) ? $disabled : '';
			$user['message'] = '';
		}
		$tpl->assign('form', isset($form) ? $form : $user);
	} else {
		$defaults = array(
			'name' => '',
			'name_disabled' => '',
			'mail' => '',
			'mail_disabled' => '',
			'website' => '',
			'website_disabled' => '',
			'message' => '',
		);

		$tpl->assign('form', isset($form) ? $form : $defaults);
	}
	
	$tpl->assign('captcha', captcha());

	$content = $tpl->fetch('guestbook/create.html');
}
?>