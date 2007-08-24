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
	include 'modules/guestbook/entry.php';
}
if (!isset($_POST['submit']) || isset($errors) && is_array($errors)) {
	// Emoticons einbinden
	if ($modules->check('emoticons', 'functions')) {
		include_once 'modules/emoticons/functions.php';
		$tpl->assign('emoticons', emoticons_list());
	}
	// Falls Benutzer eingeloggt ist, Formular schon teilweise ausfüllen
	if ($auth->is_user() && preg_match('/\d/', $_SESSION['acp3_id'])) {
		$user = $db->select('nickname, mail', 'users', 'id = \'' . $_SESSION['acp3_id'] . '\'');
		$disabled = ' readonly="readonly" class="readonly"';

		if (isset($form)) {
			$form['name'] = $user[0]['nickname'];
			$form['name_disabled'] = $disabled;
			$form['mail_disabled'] = $disabled;
		} else {
			$user[0]['name'] = $user[0]['nickname'];
			unset($user[0]['nickname']);
			$user[0]['name_disabled'] = $disabled;
			$user[0]['mail_disabled'] = $disabled;
		}
		$tpl->assign('form', isset($form) ? $form : $user[0]);
	} else {
		$defaults['name_disabled'] = '';
		$defaults['mail_disabled'] = '';

		$tpl->assign('form', isset($form) ? $form : $defaults);
	}

	$content = $tpl->fetch('guestbook/create.html');
}
?>