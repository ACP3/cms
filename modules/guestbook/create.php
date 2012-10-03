<?php
/**
 * Guestbook
 *
 * @author Tino Goratsch
 * @package ACP3
 * @subpackage Modules
 */

if (defined('IN_ACP3') === false)
	exit;

ACP3_CMS::$breadcrumb
->append(ACP3_CMS::$lang->t('guestbook', 'guestbook'), ACP3_CMS::$uri->route('guestbook'))
->append(ACP3_CMS::$lang->t('guestbook', 'create'));

$settings = ACP3_Config::getSettings('guestbook');
$newsletterAccess = ACP3_Modules::check('newsletter', 'list') === true && $settings['newsletter_integration'] == 1;
$captchaAccess = ACP3_Modules::check('captcha', 'functions');

if (ACP3_CMS::$uri->layout === 'simple') {
	$overlay_active = 1;
	ACP3_CMS::$view->setLayout('simple.tpl');
} else {
	$overlay_active = 0;
}

if (isset($_POST['submit']) === true) {
	$ip = $_SERVER['REMOTE_ADDR'];

	// Flood Sperre
	$flood = ACP3_CMS::$db2->fetchColumn('SELECT MAX(date) FROM ' . DB_PRE . 'guestbook WHERE ip = ?', array($ip));
	if (!empty($flood)) {
		$flood_time = ACP3_CMS::$date->timestamp($flood) + CONFIG_FLOOD;
	}
	$time = ACP3_CMS::$date->timestamp();

	if (isset($flood_time) && $flood_time > $time)
		$errors[] = sprintf(ACP3_CMS::$lang->t('system', 'flood_no_entry_possible'), $flood_time - $time);
	if (empty($_POST['name']))
		$errors['name'] = ACP3_CMS::$lang->t('system', 'name_to_short');
	if (!empty($_POST['mail']) && ACP3_Validate::email($_POST['mail']) === false)
		$errors['mail'] = ACP3_CMS::$lang->t('system', 'wrong_email_format');
	if (strlen($_POST['message']) < 3)
		$errors['message'] = ACP3_CMS::$lang->t('system', 'message_to_short');
	if ($captchaAccess === true && ACP3_CMS::$auth->isUser() === false && ACP3_Validate::captcha($_POST['captcha']) === false)
		$errors['captcha'] = ACP3_CMS::$lang->t('captcha', 'invalid_captcha_entered');
	if ($newsletterAccess === true && isset($_POST['subscribe_newsletter']) && $_POST['subscribe_newsletter'] == 1) {
		if (ACP3_Validate::email($_POST['mail']) === false)
			$errors['mail'] = ACP3_CMS::$lang->t('guestbook', 'type_in_email_address_to_subscribe_to_newsletter');
		if (ACP3_Validate::email($_POST['mail']) === true &&
			ACP3_CMS::$db2->fetchColumn('SELECT COUNT(*) FROM ' . DB_PRE . 'newsletter_accounts WHERE mail = ?', array($_POST['mail'])) == 1)
			$errors[] = ACP3_CMS::$lang->t('newsletter', 'account_exists');
	}

	if (isset($errors) === true) {
		ACP3_CMS::$view->assign('error_msg', errorBox($errors));
	} elseif (ACP3_Validate::formToken() === false) {
		ACP3_CMS::setContent(errorBox(ACP3_CMS::$lang->t('system', 'form_already_submitted')));
	} else {
		$insert_values = array(
			'id' => '',
			'date' => ACP3_CMS::$date->getCurrentDateTime(),
			'ip' => $ip,
			'name' => str_encode($_POST['name']),
			'user_id' => ACP3_CMS::$auth->isUser() ? ACP3_CMS::$auth->getUserId() : '',
			'message' => str_encode($_POST['message']),
			'website' => str_encode($_POST['website']),
			'mail' => $_POST['mail'],
			'active' => $settings['notify'] == 2 ? 0 : 1,
		);

		$bool = ACP3_CMS::$db2->insert(DB_PRE . 'guestbook', $insert_values);

		// E-Mail-Benachrichtigung bei neuem Eintrag der hinterlegten
		// E-Mail-Adresse zusenden
		if ($settings['notify'] == 1 || $settings['notify'] == 2) {
			$host = 'http://' . htmlentities($_SERVER['HTTP_HOST']);
			$fullPath = $host . ACP3_CMS::$uri->route('guestbook/list') . '#gb-entry-' . ACP3_CMS::$db2->lastInsertId();
			$body = sprintf($settings['notify'] == 1 ? ACP3_CMS::$lang->t('guestbook', 'notification_email_body_1') : ACP3_CMS::$lang->t('guestbook', 'notification_email_body_2'), $host, $fullPath);
			generateEmail('', $settings['notify_email'], $settings['notify_email'], ACP3_CMS::$lang->t('guestbook', 'notification_email_subject'), $body);
		}

		// Falls es der Benutzer ausgewählt hat, diesen in den Newsletter eintragen
		if ($newsletterAccess === true && isset($_POST['subscribe_newsletter']) && $_POST['subscribe_newsletter'] == 1) {
			require MODULES_DIR . 'newsletter/functions.php';
			subscribeToNewsletter($_POST['mail']);
		}

		ACP3_CMS::$session->unsetFormToken();

		setRedirectMessage($bool, ACP3_CMS::$lang->t('system', $bool !== false ? 'create_success' : 'create_error'), 'guestbook', (bool) $overlay_active);
	}
}
if (isset($_POST['submit']) === false || isset($errors) === true && is_array($errors) === true) {
	// Emoticons einbinden
	if (ACP3_Modules::check('emoticons', 'functions') === true && $settings['emoticons'] == 1) {
		require_once MODULES_DIR . 'emoticons/functions.php';
		ACP3_CMS::$view->assign('emoticons', emoticonsList());
	}

	// In Newsletter integrieren
	if ($newsletterAccess === true) {
		ACP3_CMS::$view->assign('subscribe_newsletter', selectEntry('subscribe_newsletter', '1', '1', 'checked'));
		ACP3_CMS::$view->assign('LANG_subscribe_to_newsletter', sprintf(ACP3_CMS::$lang->t('guestbook', 'subscribe_to_newsletter'), CONFIG_SEO_TITLE));
	}

	// Falls Benutzer eingeloggt ist, Formular schon teilweise ausfüllen
	if (ACP3_CMS::$auth->isUser() === true) {
		$user = ACP3_CMS::$auth->getUserInfo();
		$disabled = ' readonly="readonly" class="readonly"';

		if (isset($_POST['submit'])) {
			$_POST['name'] = $user['nickname'];
			$_POST['name_disabled'] = $disabled;
			$_POST['mail'] = $user['mail'];
			$_POST['mail_disabled'] = $disabled;
			$_POST['website_disabled'] = !empty($user['website']) ? $disabled : '';
		} else {
			$user['name'] = $user['nickname'];
			$user['name_disabled'] = $disabled;
			$user['mail_disabled'] = $disabled;
			$user['website_disabled'] = !empty($user['website']) ? $disabled : '';
			$user['message'] = '';
		}
		ACP3_CMS::$view->assign('form', isset($_POST['submit']) ? $_POST : $user);
	} else {
		$defaults = array(
			'name' => '',
			'name_disabled' => '',
			'mail' => '',
			'mail_disabled' => '',
			'website' => '',
			'website_disabled' => '',
			'message' => '',
		);

		ACP3_CMS::$view->assign('form', isset($_POST['submit']) ? array_merge($defaults, $_POST) : $defaults);
	}

	if ($captchaAccess === true) {
		require_once MODULES_DIR . 'captcha/functions.php';
		ACP3_CMS::$view->assign('captcha', captcha());
	}

	ACP3_CMS::$session->generateFormToken();

	ACP3_CMS::setContent(ACP3_CMS::$view->fetchTemplate('guestbook/create.tpl'));
}