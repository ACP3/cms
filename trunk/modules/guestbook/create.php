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

$breadcrumb->assign(lang('guestbook', 'guestbook'), uri('guestbook'));
$breadcrumb->assign(lang('guestbook', 'create'));

if (isset($_POST['submit'])) {
	$ip = $_SERVER['REMOTE_ADDR'];
	$form = $_POST['form'];

	// Flood Sperre
	$flood = $db->select('date', 'guestbook', 'ip = \'' . $ip . '\'', 'id DESC', '1');
	if (count($flood) == '1') {
		$flood_time = $flood[0]['date'] + CONFIG_FLOOD;
	}
	$time = date_aligned(2, time());

	if (isset($flood_time) && $flood_time > $time)
		$errors[] = sprintf(lang('common', 'flood_no_entry_possible'), $flood_time - $time);
	if (empty($form['name']))
		$errors[] = lang('common', 'name_to_short');
	if (!empty($form['mail']) && !$validate->email($form['mail']))
		$errors[] = lang('common', 'wrong_email_format');
	if (strlen($form['message']) < 3)
		$errors[] = lang('common', 'message_to_short');

	if (isset($errors)) {
		combo_box($errors);
	} else {
		$insert_values = array(
			'id' => '',
			'ip' => $ip,
			'date' => $time,
			'name' => $db->escape($form['name']),
			'user_id' => $auth->is_user() && preg_match('/\d/', $_SESSION['acp3_id']) ? $_SESSION['acp3_id'] : '',
			'message' => $db->escape($form['message']),
			'website' => $db->escape($form['website'], 2),
			'mail' => $form['mail'],
		);

		$bool = $db->insert('guestbook', $insert_values);

		$content = combo_box($bool ? lang('guestbook', 'create_success') : lang('guestbook', 'create_error'), uri('guestbook'));
	}
}
if (!isset($_POST['submit']) || isset($errors) && is_array($errors)) {
	// Emoticons einbinden
	if ($modules->check('emoticons', 'functions')) {
		include_once 'modules/emoticons/functions.php';
		$tpl->assign('emoticons', emoticons_list());
	}
	// Falls Benutzer eingeloggt ist, Formular schon teilweise ausfüllen
	if ($auth->is_user() && preg_match('/\d/', $_SESSION['acp3_id'])) {
		$user = $auth->getUserInfo('nickname, mail');
		$disabled = ' readonly="readonly" class="readonly"';

		if (isset($form)) {
			$form['name'] = $user['nickname'];
			$form['name_disabled'] = $disabled;
			$form['mail_disabled'] = $disabled;
		} else {
			$user['name'] = $user['nickname'];
			unset($user['nickname']);
			$user['name_disabled'] = $disabled;
			$user['mail_disabled'] = $disabled;
		}
		$tpl->assign('form', isset($form) ? $form : $user);
	} else {
		$defaults['name_disabled'] = '';
		$defaults['mail_disabled'] = '';

		$tpl->assign('form', isset($form) ? $form : $defaults);
	}

	$content = $tpl->fetch('guestbook/create.html');
}
?>