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

if (validate::email($modules->mail) && validate::isMD5($modules->hash)) {
	$mail = $modules->mail;
	$hash = $modules->hash;
} else {
	redirect('errors/404');
}

if ($db->select('id', 'newsletter_accounts', 'mail = \'' . $mail . '\' AND hash = \'' . $db->escape($hash, 2) . '\'', 0, 0, 0, 1) != 1)
	$errors[] = lang('newsletter', 'account_not_exists');

if (isset($errors)) {
	$tpl->assign('error_msg', comboBox($errors));
} else {
	$bool = $db->update('newsletter_accounts', array('hash', ''), 'mail = \'' . $mail . '\' AND hash = \'' . $db->escape($hash, 2) . '\'');

	$content = comboBox($bool ? lang('newsletter', 'activate_success') : lang('newsletter', 'activate_error'), ROOT_DIR);
}
?>