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

if (ACP3\Core\Validate::email(ACP3\CMS::$injector['URI']->mail) && ACP3\Core\Validate::isMD5(ACP3\CMS::$injector['URI']->hash)) {
	$mail = ACP3\CMS::$injector['URI']->mail;
	$hash = ACP3\CMS::$injector['URI']->hash;
} else {
	ACP3\CMS::$injector['URI']->redirect('errors/404');
}

if (ACP3\CMS::$injector['Db']->fetchColumn('SELECT COUNT(*) FROM ' . DB_PRE . 'newsletter_accounts WHERE mail = ? AND has = ?', array($mail, $hash)) != 1)
	$errors[] = ACP3\CMS::$injector['Lang']->t('newsletter', 'account_not_exists');

if (isset($errors) === true) {
	ACP3\CMS::$injector['View']->setContent(Core\Functions::errorBox($errors));
} else {
	$bool = ACP3\CMS::$injector['Db']->update(DB_PRE . 'newsletter_accounts', array('hash' => ''), array('mail' => $mail, 'hash' => $hash));

	ACP3\CMS::$injector['View']->setContent(confirmBox(ACP3\CMS::$injector['Lang']->t('newsletter', $bool !== false ? 'activate_success' : 'activate_error'), ROOT_DIR));
}
