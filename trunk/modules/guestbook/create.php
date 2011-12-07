<?php
/**
 * Guestbook
 *
 * @author Goratsch Webdesign
 * @package ACP3
 * @subpackage Modules
 */

if (!defined('IN_ACP3'))
	exit;

breadcrumb::assign($lang->t('guestbook', 'guestbook'), uri('guestbook'));
breadcrumb::assign($lang->t('guestbook', 'create'));

$settings = config::output('guestbook');
$newsletterAccess = modules::check('newsletter', 'create') == 1 && $settings['newsletter_integration'] == 1;

if ($uri->design == 'simple') {
	$comboColorbox = 1;
	define('CUSTOM_LAYOUT', 'simple.html');
} else {
	$comboColorbox = 0;
}

if (isset($_POST['form'])) {
	$ip = $_SERVER['REMOTE_ADDR'];
	$form = $_POST['form'];

	// Flood Sperre
	$flood = $db->select('date', 'guestbook', 'ip = \'' . $ip . '\'', 'id DESC', '1');
	if (count($flood) == '1') {
		$flood_time = $flood[0]['date'] + CONFIG_FLOOD;
	}
	$time = $date->timestamp();

	if (isset($flood_time) && $flood_time > $time)
		$errors[] = sprintf($lang->t('common', 'flood_no_entry_possible'), $flood_time - $time);
	if (empty($form['name']))
		$errors[] = $lang->t('common', 'name_to_short');
	if (!empty($form['mail']) && !validate::email($form['mail']))
		$errors[] = $lang->t('common', 'wrong_email_format');
	if (strlen($form['message']) < 3)
		$errors[] = $lang->t('common', 'message_to_short');
	if (!$auth->isUser() && !validate::captcha($form['captcha'], $form['hash']))
		$errors[] = $lang->t('captcha', 'invalid_captcha_entered');
	if ($newsletterAccess) {
		if ($form['subscribe_newsletter'] == 1 && !validate::email($form['mail']))
			$errors[] = $lang->t('guestbook', 'type_in_email_address_to_subscribe_to_newsletter');
		if ($form['subscribe_newsletter'] == 1 && validate::email($form['mail']) &&
			$db->countRows('*', 'newsletter_accounts', 'mail = \'' . $form['mail'] . '\'') == 1)
			$errors[] = $lang->t('newsletter', 'account_exists');
	}

	if (isset($errors)) {
		$tpl->assign('error_msg', comboBox($errors));
	} else {
		$insert_values = array(
			'id' => '',
			'ip' => $ip,
			'date' => $time,
			'name' => $db->escape($form['name']),
			'user_id' => $auth->isUser() ? $auth->getUserId() : '',
			'message' => $db->escape($form['message']),
			'website' => $db->escape($form['website'], 2),
			'mail' => $form['mail'],
			'active' => $settings['notify'] == 2 ? 0 : 1,
		);

		$bool = $db->insert('guestbook', $insert_values);

		// E-Mail-Benachrichtigung bei neuem Eintrag der hinterlegten
		// E-Mail-Adresse zusenden
		if ($settings['notify'] == 1 || $settings['notify'] == 2) {
			$host = 'http://' . htmlentities($_SERVER['HTTP_HOST']);
			$fullPath = $host . uri('guestbook/list', 1) . '#gb-entry-' . $db->link->lastInsertId();
			$body = sprintf($settings['notify'] == 1 ? $lang->t('guestbook', 'notification_email_body_1') : $lang->t('guestbook', 'notification_email_body_2'), $host, $fullPath);
			genEmail('', $settings['notify_email'], $settings['notify_email'], $lang->t('guestbook', 'notification_email_subject'), $body);
		}

		// Falls es der Benutzer ausgewählt hat, diesen in den Newsletter eintragen
		if ($newsletterAccess == 1 && $form['subscribe_newsletter'] == 1) {
			require MODULES_DIR . 'newsletter/functions.php';
			subscribeToNewsletter($form['mail']);
		}

		$content = comboBox($bool ? $lang->t('common', 'create_success') : $lang->t('common', 'create_error'), uri('guestbook'), 0, $comboColorbox);
	}
}
if (!isset($_POST['form']) || isset($errors) && is_array($errors)) {
	// Emoticons einbinden
	if (modules::check('emoticons', 'functions') == 1 && $settings['emoticons'] == 1) {
		require_once MODULES_DIR . 'emoticons/functions.php';
		$tpl->assign('emoticons', emoticonsList());
	}

	// In Newsletter integrieren
	if ($newsletterAccess == 1) {
		$tpl->assign('subscribe_newsletter', selectEntry('subscribe_newsletter', '1', '1', 'checked'));
		$tpl->assign('LANG_subscribe_to_newsletter', sprintf($lang->t('guestbook', 'subscribe_to_newsletter'), CONFIG_SEO_TITLE));
	}

	// Falls Benutzer eingeloggt ist, Formular schon teilweise ausfüllen
	if ($auth->isUser()) {
		$user = $auth->getUserInfo();
		$disabled = ' readonly="readonly" class="readonly"';

		if (isset($form)) {
			$form['name'] = $user['nickname'];
			$form['name_disabled'] = $disabled;
			$form['mail'] = $user['mail'];
			$form['mail_disabled'] = $disabled;
			$form['website_disabled'] = !empty($user['website']) ? $disabled : '';
		} else {
			$user['name'] = $user['nickname'];
			$user['name_disabled'] = $disabled;
			$user['mail_disabled'] = $disabled;
			$user['website_disabled'] = !empty($user['website']) ? $disabled : '';
			$user['message'] = '';
		}
		$tpl->assign('form', isset($form) ? $form : $user);
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

		$tpl->assign('form', isset($form) ? array_merge($defaults, $form) : $defaults);
	}
	
	$tpl->assign('captcha', captcha());

	$content = modules::fetchTemplate('guestbook/create.html');
}
