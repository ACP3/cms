<?php
/**
 * Newsletter
 *
 * @author Goratsch Webdesign
 * @package ACP3
 * @subpackage Modules
 */

if (defined('IN_ACP3') === false)
	exit;

if (validate::email($uri->mail) && validate::isMD5($uri->hash)) {
	$mail = $uri->mail;
	$hash = $uri->hash;
} else {
	$uri->redirect('errors/404');
}

if ($db->countRows('*', 'newsletter_accounts', 'mail = \'' . $mail . '\' AND hash = \'' . $db->escape($hash, 2) . '\'') != 1)
	$errors[] = $lang->t('newsletter', 'account_not_exists');

if (isset($errors)) {
	$tpl->assign('error_msg', comboBox($errors));
} else {
	$bool = $db->update('newsletter_accounts', array('hash' => ''), 'mail = \'' . $mail . '\' AND hash = \'' . $db->escape($hash, 2) . '\'');

	view::setContent(comboBox($bool !== null ? $lang->t('newsletter', 'activate_success') : $lang->t('newsletter', 'activate_error'), ROOT_DIR));
}
