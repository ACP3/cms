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

if (ACP3_CMS::$db2->fetchColumn('SELECT COUNT(*) FROM ' . DB_PRE . 'newsletter_accounts WHERE mail = ? AND has = ?', array($mail, $hash)) != 1)
	$errors[] = ACP3_CMS::$lang->t('newsletter', 'account_not_exists');

if (isset($errors) === true) {
	ACP3_CMS::$view->setContent(errorBox($errors));
} else {
	$bool = ACP3_CMS::$db2->update(DB_PRE . 'newsletter_accounts', array('hash' => ''), array('mail' => $mail, 'hash' => $hash));

	ACP3_CMS::$view->setContent(confirmBox(ACP3_CMS::$lang->t('newsletter', $bool !== false ? 'activate_success' : 'activate_error'), ROOT_DIR));
}
