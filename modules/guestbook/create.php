<?php
/**
 * Guestbook
 *
 * @author Goratsch Webdesign
 * @package ACP3
 * @subpackage Modules
 */

if (defined('IN_ACP3') === false)
	exit;

$breadcrumb->append($lang->t('guestbook', 'guestbook'), $uri->route('guestbook'))
		   ->append($lang->t('guestbook', 'create'));

$settings = ACP3_Config::getModuleSettings('guestbook');
$newsletterAccess = ACP3_Modules::check('newsletter', 'create') === true && $settings['newsletter_integration'] == 1;

if ($uri->layout === 'simple') {
	$overlay_active = 1;
	ACP3_View::assignLayout('simple.tpl');
} else {
	$overlay_active = 0;
}

if (isset($_POST['submit']) === true) {
	$ip = $_SERVER['REMOTE_ADDR'];

	// Flood Sperre
	$flood = $db->select('date', 'guestbook', 'ip = \'' . $ip . '\'', 'id DESC', '1');
	if (count($flood) === 1) {
		$flood_time = $flood[0]['date'] + CONFIG_FLOOD;
	}
	$time = $date->timestamp();

	if (isset($flood_time) && $flood_time > $time)
		$errors[] = sprintf($lang->t('common', 'flood_no_entry_possible'), $flood_time - $time);
	if (empty($_POST['name']))
		$errors['name'] = $lang->t('common', 'name_to_short');
	if (!empty($_POST['mail']) && ACP3_Validate::email($_POST['mail']) === false)
		$errors['mail'] = $lang->t('common', 'wrong_email_format');
	if (strlen($_POST['message']) < 3)
		$errors['message'] = $lang->t('common', 'message_to_short');
	if ($auth->isUser() === false && ACP3_Validate::captcha($_POST['captcha']) === false)
		$errors['captcha'] = $lang->t('captcha', 'invalid_captcha_entered');
	if ($newsletterAccess === true && isset($_POST['subscribe_newsletter']) && $_POST['subscribe_newsletter'] == 1) {
		if (ACP3_Validate::email($_POST['mail']) === false)
			$errors['mail'] = $lang->t('guestbook', 'type_in_email_address_to_subscribe_to_newsletter');
		if (ACP3_Validate::email($_POST['mail']) === true &&
			$db->countRows('*', 'newsletter_accounts', 'mail = \'' . $_POST['mail'] . '\'') == 1)
			$errors[] = $lang->t('newsletter', 'account_exists');
	}

	if (isset($errors) === true) {
		$tpl->assign('error_msg', errorBox($errors));
	} elseif (ACP3_Validate::formToken() === false) {
		ACP3_View::setContent(errorBox($lang->t('common', 'form_already_submitted')));
	} else {
		$insert_values = array(
			'id' => '',
			'ip' => $ip,
			'date' => $time,
			'name' => $db->escape($_POST['name']),
			'user_id' => $auth->isUser() ? $auth->getUserId() : '',
			'message' => $db->escape($_POST['message']),
			'website' => $db->escape($_POST['website'], 2),
			'mail' => $_POST['mail'],
			'active' => $settings['notify'] == 2 ? 0 : 1,
		);

		$bool = $db->insert('guestbook', $insert_values);

		// E-Mail-Benachrichtigung bei neuem Eintrag der hinterlegten
		// E-Mail-Adresse zusenden
		if ($settings['notify'] == 1 || $settings['notify'] == 2) {
			$host = 'http://' . htmlentities($_SERVER['HTTP_HOST']);
			$fullPath = $host . $uri->route('guestbook/list', 1) . '#gb-entry-' . $db->link->lastInsertId();
			$body = sprintf($settings['notify'] == 1 ? $lang->t('guestbook', 'notification_email_body_1') : $lang->t('guestbook', 'notification_email_body_2'), $host, $fullPath);
			generateEmail('', $settings['notify_email'], $settings['notify_email'], $lang->t('guestbook', 'notification_email_subject'), $body);
		}

		// Falls es der Benutzer ausgewählt hat, diesen in den Newsletter eintragen
		if ($newsletterAccess === true && isset($_POST['subscribe_newsletter']) && $_POST['subscribe_newsletter'] == 1) {
			require MODULES_DIR . 'newsletter/functions.php';
			subscribeToNewsletter($_POST['mail']);
		}

		$session->unsetFormToken();

		ACP3_View::setContent(confirmBox($bool !== false ? $lang->t('common', 'create_success') : $lang->t('common', 'create_error'), $uri->route('guestbook'), 0, $overlay_active));
	}
}
if (isset($_POST['submit']) === false || isset($errors) === true && is_array($errors) === true) {
	// Emoticons einbinden
	if (ACP3_Modules::check('emoticons', 'functions') === true && $settings['emoticons'] == 1) {
		require_once MODULES_DIR . 'emoticons/functions.php';
		$tpl->assign('emoticons', emoticonsList());
	}

	// In Newsletter integrieren
	if ($newsletterAccess == 1) {
		$tpl->assign('subscribe_newsletter', selectEntry('subscribe_newsletter', '1', '1', 'checked'));
		$tpl->assign('LANG_subscribe_to_newsletter', sprintf($lang->t('guestbook', 'subscribe_to_newsletter'), CONFIG_SEO_TITLE));
	}

	// Falls Benutzer eingeloggt ist, Formular schon teilweise ausfüllen
	if ($auth->isUser() === true) {
		$user = $auth->getUserInfo();
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
		$tpl->assign('form', isset($_POST['submit']) ? $_POST : $user);
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

		$tpl->assign('form', isset($_POST['submit']) ? array_merge($defaults, $_POST) : $defaults);
	}

	$tpl->assign('captcha', captcha());

	$session->generateFormToken();

	ACP3_View::setContent(ACP3_View::fetchTemplate('guestbook/create.tpl'));
}