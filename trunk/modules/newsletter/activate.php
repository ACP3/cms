<?php
/**
 * Newsletter
 *
 * @author Tino Goratsch
 * @package ACP3
 * @subpackage Modules
 */

if (defined('IN_ACP3') === false)
	exit;

if (ACP3_Validate::email(ACP3_CMS::$uri->mail) && ACP3_Validate::isMD5(ACP3_CMS::$uri->hash)) {
	$mail = ACP3_CMS::$uri->mail;
	$hash = ACP3_CMS::$uri->hash;
} else {
	ACP3_CMS::$uri->redirect('errors/404');
}

if (ACP3_CMS::$db->countRows('*', 'newsletter_accounts', 'mail = \'' . $mail . '\' AND hash = \'' . ACP3_CMS::$db->escape($hash, 2) . '\'') != 1)
	$errors[] = ACP3_CMS::$lang->t('newsletter', 'account_not_exists');

if (isset($errors) === true) {
	ACP3_CMS::setContent(errorBox($errors));
} else {
	$bool = ACP3_CMS::$db->update('newsletter_accounts', array('hash' => ''), 'mail = \'' . $mail . '\' AND hash = \'' . ACP3_CMS::$db->escape($hash, 2) . '\'');

	ACP3_CMS::setContent(confirmBox($bool !== false ? ACP3_CMS::$lang->t('newsletter', 'activate_success') : ACP3_CMS::$lang->t('newsletter', 'activate_error'), ROOT_DIR));
}
