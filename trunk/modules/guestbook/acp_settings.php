<?php
/**
 * Guestbook
 *
 * @author Tino Goratsch
 * @package ACP3
 * @subpackage Modules
 */

if (defined('IN_ADM') === false)
	exit;

$emoticons_active = ACP3_Modules::isActive('emoticons');
$newsletter_active = ACP3_Modules::isActive('newsletter');

if (isset($_POST['submit']) === true) {
	if (empty($_POST['dateformat']) || ($_POST['dateformat'] !== 'long' && $_POST['dateformat'] !== 'short'))
		$errors['dateformat'] = ACP3_CMS::$lang->t('system', 'select_date_format');
	if (!isset($_POST['notify']) || ($_POST['notify'] != 0 && $_POST['notify'] != 1 && $_POST['notify'] != 2))
		$errors['notify'] = ACP3_CMS::$lang->t('guestbook', 'select_notification_type');
	if ($_POST['notify'] != 0 && ACP3_Validate::email($_POST['notify_email']) === false)
		$errors['notify-email'] = ACP3_CMS::$lang->t('system', 'wrong_email_format');
	if (!isset($_POST['overlay']) || $_POST['overlay'] != 1 && $_POST['overlay'] != 0)
		$errors[] = ACP3_CMS::$lang->t('guestbook', 'select_use_overlay');
	if ($emoticons_active === true && (!isset($_POST['emoticons']) || ($_POST['emoticons'] != 0 && $_POST['emoticons'] != 1)))
		$errors[] = ACP3_CMS::$lang->t('guestbook', 'select_emoticons');
	if ($newsletter_active === true && (!isset($_POST['newsletter_integration']) || ($_POST['newsletter_integration'] != 0 && $_POST['newsletter_integration'] != 1)))
		$errors[] = ACP3_CMS::$lang->t('guestbook', 'select_newsletter_integration');

	if (isset($errors) === true) {
		ACP3_CMS::$view->assign('error_msg', errorBox($errors));
	} elseif (ACP3_Validate::formToken() === false) {
		ACP3_CMS::$view->setContent(errorBox(ACP3_CMS::$lang->t('system', 'form_already_submitted')));
	} else {
		$data = array(
			'dateformat' => str_encode($_POST['dateformat']),
			'notify' => $_POST['notify'],
			'notify_email' => $_POST['notify_email'],
			'overlay' => $_POST['overlay'],
			'emoticons' => $_POST['emoticons'],
			'newsletter_integration' => $_POST['newsletter_integration'],
		);
		$bool = ACP3_Config::setSettings('guestbook', $data);

		ACP3_CMS::$session->unsetFormToken();

		setRedirectMessage($bool, ACP3_CMS::$lang->t('system', $bool === true ? 'settings_success' : 'settings_error'), 'acp/guestbook');
	}
}
if (isset($_POST['submit']) === false || isset($errors) === true && is_array($errors) === true) {
	$settings = ACP3_Config::getSettings('guestbook');

	ACP3_CMS::$view->assign('dateformat', ACP3_CMS::$date->dateformatDropdown($settings['dateformat']));

	$lang_notify = array(
		ACP3_CMS::$lang->t('guestbook', 'no_notification'),
		ACP3_CMS::$lang->t('guestbook', 'notify_on_new_entry'),
		ACP3_CMS::$lang->t('guestbook', 'notify_and_enable')
	);
	ACP3_CMS::$view->assign('notify', selectGenerator('notify', array(0, 1, 2), $lang_notify, $settings['notify']));

	$lang_overlay = array(ACP3_CMS::$lang->t('system', 'yes'), ACP3_CMS::$lang->t('system', 'no'));
	ACP3_CMS::$view->assign('overlay', selectGenerator('overlay', array(1, 0), $lang_overlay, $settings['overlay'], 'checked'));

	// Emoticons erlauben
	if ($emoticons_active === true) {
		$lang_allow_emoticons = array(ACP3_CMS::$lang->t('system', 'yes'), ACP3_CMS::$lang->t('system', 'no'));
		ACP3_CMS::$view->assign('allow_emoticons', selectGenerator('emoticons', array(1, 0), $lang_allow_emoticons, $settings['emoticons'], 'checked'));
	}

	// In Newsletter integrieren
	if ($newsletter_active === true) {
		$lang_newsletter_integration = array(ACP3_CMS::$lang->t('system', 'yes'), ACP3_CMS::$lang->t('system', 'no'));
		ACP3_CMS::$view->assign('newsletter_integration', selectGenerator('newsletter_integration', array(1, 0), $lang_newsletter_integration, $settings['newsletter_integration'], 'checked'));
	}

	ACP3_CMS::$view->assign('form', isset($_POST['submit']) ? $_POST : array('notify_email' => $settings['notify_email']));

	ACP3_CMS::$session->generateFormToken();
}