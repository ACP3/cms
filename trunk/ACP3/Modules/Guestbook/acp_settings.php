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

$emoticons_active = ACP3\Core\Modules::isActive('emoticons');
$newsletter_active = ACP3\Core\Modules::isActive('newsletter');

if (isset($_POST['submit']) === true) {
	if (empty($_POST['dateformat']) || ($_POST['dateformat'] !== 'long' && $_POST['dateformat'] !== 'short'))
		$errors['dateformat'] = ACP3\CMS::$injector['Lang']->t('system', 'select_date_format');
	if (!isset($_POST['notify']) || ($_POST['notify'] != 0 && $_POST['notify'] != 1 && $_POST['notify'] != 2))
		$errors['notify'] = ACP3\CMS::$injector['Lang']->t('guestbook', 'select_notification_type');
	if ($_POST['notify'] != 0 && ACP3\Core\Validate::email($_POST['notify_email']) === false)
		$errors['notify-email'] = ACP3\CMS::$injector['Lang']->t('system', 'wrong_email_format');
	if (!isset($_POST['overlay']) || $_POST['overlay'] != 1 && $_POST['overlay'] != 0)
		$errors[] = ACP3\CMS::$injector['Lang']->t('guestbook', 'select_use_overlay');
	if ($emoticons_active === true && (!isset($_POST['emoticons']) || ($_POST['emoticons'] != 0 && $_POST['emoticons'] != 1)))
		$errors[] = ACP3\CMS::$injector['Lang']->t('guestbook', 'select_emoticons');
	if ($newsletter_active === true && (!isset($_POST['newsletter_integration']) || ($_POST['newsletter_integration'] != 0 && $_POST['newsletter_integration'] != 1)))
		$errors[] = ACP3\CMS::$injector['Lang']->t('guestbook', 'select_newsletter_integration');

	if (isset($errors) === true) {
		ACP3\CMS::$injector['View']->assign('error_msg', Core\Functions::errorBox($errors));
	} elseif (ACP3\Core\Validate::formToken() === false) {
		ACP3\CMS::$injector['View']->setContent(Core\Functions::errorBox(ACP3\CMS::$injector['Lang']->t('system', 'form_already_submitted')));
	} else {
		$data = array(
			'dateformat' => ACP3\Core\Functions::str_encode($_POST['dateformat']),
			'notify' => $_POST['notify'],
			'notify_email' => $_POST['notify_email'],
			'overlay' => $_POST['overlay'],
			'emoticons' => $_POST['emoticons'],
			'newsletter_integration' => $_POST['newsletter_integration'],
		);
		$bool = ACP3\Core\Config::setSettings('guestbook', $data);

		ACP3\CMS::$injector['Session']->unsetFormToken();

		ACP3\Core\Functions::setRedirectMessage($bool, ACP3\CMS::$injector['Lang']->t('system', $bool === true ? 'settings_success' : 'settings_error'), 'acp/guestbook');
	}
}
if (isset($_POST['submit']) === false || isset($errors) === true && is_array($errors) === true) {
	$settings = ACP3\Core\Config::getSettings('guestbook');

	ACP3\CMS::$injector['View']->assign('dateformat', ACP3\CMS::$injector['Date']->dateformatDropdown($settings['dateformat']));

	$lang_notify = array(
		ACP3\CMS::$injector['Lang']->t('guestbook', 'no_notification'),
		ACP3\CMS::$injector['Lang']->t('guestbook', 'notify_on_new_entry'),
		ACP3\CMS::$injector['Lang']->t('guestbook', 'notify_and_enable')
	);
	ACP3\CMS::$injector['View']->assign('notify', ACP3\Core\Functions::selectGenerator('notify', array(0, 1, 2), $lang_notify, $settings['notify']));

	$lang_overlay = array(ACP3\CMS::$injector['Lang']->t('system', 'yes'), ACP3\CMS::$injector['Lang']->t('system', 'no'));
	ACP3\CMS::$injector['View']->assign('overlay', ACP3\Core\Functions::selectGenerator('overlay', array(1, 0), $lang_overlay, $settings['overlay'], 'checked'));

	// Emoticons erlauben
	if ($emoticons_active === true) {
		$lang_allow_emoticons = array(ACP3\CMS::$injector['Lang']->t('system', 'yes'), ACP3\CMS::$injector['Lang']->t('system', 'no'));
		ACP3\CMS::$injector['View']->assign('allow_emoticons', ACP3\Core\Functions::selectGenerator('emoticons', array(1, 0), $lang_allow_emoticons, $settings['emoticons'], 'checked'));
	}

	// In Newsletter integrieren
	if ($newsletter_active === true) {
		$lang_newsletter_integration = array(ACP3\CMS::$injector['Lang']->t('system', 'yes'), ACP3\CMS::$injector['Lang']->t('system', 'no'));
		ACP3\CMS::$injector['View']->assign('newsletter_integration', ACP3\Core\Functions::selectGenerator('newsletter_integration', array(1, 0), $lang_newsletter_integration, $settings['newsletter_integration'], 'checked'));
	}

	ACP3\CMS::$injector['View']->assign('form', isset($_POST['submit']) ? $_POST : array('notify_email' => $settings['notify_email']));

	ACP3\CMS::$injector['Session']->generateFormToken();
}