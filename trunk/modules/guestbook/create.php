<?php
/**
 * Guestbook
 *
 * @author Goratsch Webdesign
 * @package ACP3
 * @subpackage Modules
 */

if (!defined('IN_FRONTEND'))
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