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

ACP3\CMS::$injector['Breadcrumb']
->append(ACP3\CMS::$injector['Lang']->t('guestbook', 'guestbook'), ACP3\CMS::$injector['URI']->route('guestbook'))
->append(ACP3\CMS::$injector['Lang']->t('guestbook', 'create'));

$settings = ACP3\Core\Config::getSettings('guestbook');
$newsletterAccess = ACP3\Core\Modules::check('newsletter', 'list') === true && $settings['newsletter_integration'] == 1;
$captchaAccess = ACP3\Core\Modules::check('captcha', 'functions');

if (ACP3\CMS::$injector['URI']->layout === 'simple') {
	$overlay_active = 1;
	ACP3\CMS::$injector['View']->setLayout('simple.tpl');
} else {
	$overlay_active = 0;
}

if (isset($_POST['submit']) === true) {
	$ip = $_SERVER['REMOTE_ADDR'];

	// Flood Sperre
	$flood = ACP3\CMS::$injector['Db']->fetchColumn('SELECT MAX(date) FROM ' . DB_PRE . 'guestbook WHERE ip = ?', array($ip));
	if (!empty($flood)) {
		$flood_time = ACP3\CMS::$injector['Date']->timestamp($flood) + CONFIG_FLOOD;
	}
	$time = ACP3\CMS::$injector['Date']->timestamp();

	if (isset($flood_time) && $flood_time > $time)
		$errors[] = sprintf(ACP3\CMS::$injector['Lang']->t('system', 'flood_no_entry_possible'), $flood_time - $time);
	if (empty($_POST['name']))
		$errors['name'] = ACP3\CMS::$injector['Lang']->t('system', 'name_to_short');
	if (!empty($_POST['mail']) && ACP3\Core\Validate::email($_POST['mail']) === false)
		$errors['mail'] = ACP3\CMS::$injector['Lang']->t('system', 'wrong_email_format');
	if (strlen($_POST['message']) < 3)
		$errors['message'] = ACP3\CMS::$injector['Lang']->t('system', 'message_to_short');
	if ($captchaAccess === true && ACP3\CMS::$injector['Auth']->isUser() === false && ACP3\Core\Validate::captcha($_POST['captcha']) === false)
		$errors['captcha'] = ACP3\CMS::$injector['Lang']->t('captcha', 'invalid_captcha_entered');
	if ($newsletterAccess === true && isset($_POST['subscribe_newsletter']) && $_POST['subscribe_newsletter'] == 1) {
		if (ACP3\Core\Validate::email($_POST['mail']) === false)
			$errors['mail'] = ACP3\CMS::$injector['Lang']->t('guestbook', 'type_in_email_address_to_subscribe_to_newsletter');
		if (ACP3\Core\Validate::email($_POST['mail']) === true &&
			ACP3\CMS::$injector['Db']->fetchColumn('SELECT COUNT(*) FROM ' . DB_PRE . 'newsletter_accounts WHERE mail = ?', array($_POST['mail'])) == 1)
			$errors[] = ACP3\CMS::$injector['Lang']->t('newsletter', 'account_exists');
	}

	if (isset($errors) === true) {
		ACP3\CMS::$injector['View']->assign('error_msg', Core\Functions::errorBox($errors));
	} elseif (ACP3\Core\Validate::formToken() === false) {
		ACP3\CMS::$injector['View']->setContent(Core\Functions::errorBox(ACP3\CMS::$injector['Lang']->t('system', 'form_already_submitted')));
	} else {
		$insert_values = array(
			'id' => '',
			'date' => ACP3\CMS::$injector['Date']->getCurrentDateTime(),
			'ip' => $ip,
			'name' => ACP3\Core\Functions::str_encode($_POST['name']),
			'user_id' => ACP3\CMS::$injector['Auth']->isUser() ? ACP3\CMS::$injector['Auth']->getUserId() : '',
			'message' => ACP3\Core\Functions::str_encode($_POST['message']),
			'website' => ACP3\Core\Functions::str_encode($_POST['website']),
			'mail' => $_POST['mail'],
			'active' => $settings['notify'] == 2 ? 0 : 1,
		);

		$bool = ACP3\CMS::$injector['Db']->insert(DB_PRE . 'guestbook', $insert_values);

		// E-Mail-Benachrichtigung bei neuem Eintrag der hinterlegten
		// E-Mail-Adresse zusenden
		if ($settings['notify'] == 1 || $settings['notify'] == 2) {
			$host = 'http://' . htmlentities($_SERVER['HTTP_HOST']);
			$fullPath = $host . ACP3\CMS::$injector['URI']->route('guestbook/list') . '#gb-entry-' . ACP3\CMS::$injector['Db']->lastInsertId();
			$body = sprintf($settings['notify'] == 1 ? ACP3\CMS::$injector['Lang']->t('guestbook', 'notification_email_body_1') : ACP3\CMS::$injector['Lang']->t('guestbook', 'notification_email_body_2'), $host, $fullPath);
			generateEmail('', $settings['notify_email'], $settings['notify_email'], ACP3\CMS::$injector['Lang']->t('guestbook', 'notification_email_subject'), $body);
		}

		// Falls es der Benutzer ausgewählt hat, diesen in den Newsletter eintragen
		if ($newsletterAccess === true && isset($_POST['subscribe_newsletter']) && $_POST['subscribe_newsletter'] == 1) {
			require MODULES_DIR . 'newsletter/functions.php';
			subscribeToNewsletter($_POST['mail']);
		}

		ACP3\CMS::$injector['Session']->unsetFormToken();

		ACP3\Core\Functions::setRedirectMessage($bool, ACP3\CMS::$injector['Lang']->t('system', $bool !== false ? 'create_success' : 'create_error'), 'guestbook', (bool) $overlay_active);
	}
}
if (isset($_POST['submit']) === false || isset($errors) === true && is_array($errors) === true) {
	// Emoticons einbinden
	if (ACP3\Core\Modules::check('emoticons', 'functions') === true && $settings['emoticons'] == 1) {
		require_once MODULES_DIR . 'emoticons/functions.php';
		ACP3\CMS::$injector['View']->assign('emoticons', emoticonsList());
	}

	// In Newsletter integrieren
	if ($newsletterAccess === true) {
		ACP3\CMS::$injector['View']->assign('subscribe_newsletter', ACP3\Core\Functions::selectEntry('subscribe_newsletter', '1', '1', 'checked'));
		ACP3\CMS::$injector['View']->assign('LANG_subscribe_to_newsletter', sprintf(ACP3\CMS::$injector['Lang']->t('guestbook', 'subscribe_to_newsletter'), CONFIG_SEO_TITLE));
	}

	// Falls Benutzer eingeloggt ist, Formular schon teilweise ausfüllen
	if (ACP3\CMS::$injector['Auth']->isUser() === true) {
		$user = ACP3\CMS::$injector['Auth']->getUserInfo();
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
		ACP3\CMS::$injector['View']->assign('form', isset($_POST['submit']) ? $_POST : $user);
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

		ACP3\CMS::$injector['View']->assign('form', isset($_POST['submit']) ? array_merge($defaults, $_POST) : $defaults);
	}

	if ($captchaAccess === true) {
		require_once MODULES_DIR . 'captcha/functions.php';
		ACP3\CMS::$injector['View']->assign('captcha', captcha());
	}

	ACP3\CMS::$injector['Session']->generateFormToken();
}