<?php
/**
 * Newsletter
 *
 * @author Goratsch Webdesign
 * @package ACP3
 * @subpackage Modules
 */

if (!defined('IN_ACP3'))
	exit;

if (isset($modules->gen['mail']) && $validate->email($modules->gen['mail']) && isset($modules->gen['hash']) && preg_match('/^[a-f0-9]{32}+$/', $modules->gen['hash'])) {
	$mail = $modules->gen['mail'];
	$hash = $modules->gen['hash'];
} else {
	redirect('errors/404');
}

if ($db->select('id', 'nl_accounts', 'mail = \'' . $mail . '\' AND hash = \'' . $db->escape($hash, 2) . '\'', 0, 0, 0, 1) != 1)
	$errors[] = lang('newsletter', 'nl_account_not_exists');

if (isset($errors)) {
	$tpl->assign('error_msg', combo_box($errors));
} else {
	$bool = $db->update('nl_accounts', array('hash', ''), 'mail = \'' . $mail . '\' AND hash = \'' . $db->escape($hash, 2) . '\'');

	$content = combo_box($bool ? lang('newsletter', 'nl_activate_success') : lang('newsletter', 'nl_activate_error'), ROOT_DIR);
}
?>