<?php

namespace ACP3\Modules\Guestbook;

use ACP3\Core;

/**
 * Description of GuestbookFrontend
 *
 * @author Tino Goratsch
 */
class GuestbookFrontend extends Core\ModuleController {

	public function actionCreate() {
		Core\Registry::get('Breadcrumb')
				->append(Core\Registry::get('Lang')->t('guestbook', 'guestbook'), Core\Registry::get('URI')->route('guestbook'))
				->append(Core\Registry::get('Lang')->t('guestbook', 'create'));

		$settings = Core\Config::getSettings('guestbook');
		$newsletterAccess = Core\Modules::hasPermission('newsletter', 'list') === true && $settings['newsletter_integration'] == 1;
		$captchaAccess = Core\Modules::hasPermission('captcha', 'image');

		$overlay_active = false;
		if (Core\Registry::get('URI')->layout === 'simple') {
			$overlay_active = true;
			Core\Registry::get('View')->setLayout('simple.tpl');
		}

		if (isset($_POST['submit']) === true) {
			$ip = $_SERVER['REMOTE_ADDR'];

			// Flood Sperre
			$flood = Core\Registry::get('Db')->fetchColumn('SELECT MAX(date) FROM ' . DB_PRE . 'guestbook WHERE ip = ?', array($ip));
			if (!empty($flood)) {
				$flood_time = Core\Registry::get('Date')->timestamp($flood) + CONFIG_FLOOD;
			}
			$time = Core\Registry::get('Date')->timestamp();

			if (isset($flood_time) && $flood_time > $time)
				$errors[] = sprintf(Core\Registry::get('Lang')->t('system', 'flood_no_entry_possible'), $flood_time - $time);
			if (empty($_POST['name']))
				$errors['name'] = Core\Registry::get('Lang')->t('system', 'name_to_short');
			if (!empty($_POST['mail']) && Core\Validate::email($_POST['mail']) === false)
				$errors['mail'] = Core\Registry::get('Lang')->t('system', 'wrong_email_format');
			if (strlen($_POST['message']) < 3)
				$errors['message'] = Core\Registry::get('Lang')->t('system', 'message_to_short');
			if ($captchaAccess === true && Core\Registry::get('Auth')->isUser() === false && Core\Validate::captcha($_POST['captcha']) === false)
				$errors['captcha'] = Core\Registry::get('Lang')->t('captcha', 'invalid_captcha_entered');
			if ($newsletterAccess === true && isset($_POST['subscribe_newsletter']) && $_POST['subscribe_newsletter'] == 1) {
				if (Core\Validate::email($_POST['mail']) === false)
					$errors['mail'] = Core\Registry::get('Lang')->t('guestbook', 'type_in_email_address_to_subscribe_to_newsletter');
				if (Core\Validate::email($_POST['mail']) === true &&
						Core\Registry::get('Db')->fetchColumn('SELECT COUNT(*) FROM ' . DB_PRE . 'newsletter_accounts WHERE mail = ?', array($_POST['mail'])) == 1)
					$errors[] = Core\Registry::get('Lang')->t('newsletter', 'account_exists');
			}

			if (isset($errors) === true) {
				Core\Registry::get('View')->assign('error_msg', Core\Functions::errorBox($errors));
			} elseif (Core\Validate::formToken() === false) {
				Core\Registry::get('View')->setContent(Core\Functions::errorBox(Core\Registry::get('Lang')->t('system', 'form_already_submitted')));
			} else {
				$insert_values = array(
					'id' => '',
					'date' => Core\Registry::get('Date')->getCurrentDateTime(),
					'ip' => $ip,
					'name' => Core\Functions::strEncode($_POST['name']),
					'user_id' => Core\Registry::get('Auth')->isUser() ? Core\Registry::get('Auth')->getUserId() : '',
					'message' => Core\Functions::strEncode($_POST['message']),
					'website' => Core\Functions::strEncode($_POST['website']),
					'mail' => $_POST['mail'],
					'active' => $settings['notify'] == 2 ? 0 : 1,
				);

				$bool = Core\Registry::get('Db')->insert(DB_PRE . 'guestbook', $insert_values);

				// E-Mail-Benachrichtigung bei neuem Eintrag der hinterlegten
				// E-Mail-Adresse zusenden
				if ($settings['notify'] == 1 || $settings['notify'] == 2) {
					$host = 'http://' . htmlentities($_SERVER['HTTP_HOST']);
					$fullPath = $host . Core\Registry::get('URI')->route('guestbook/list') . '#gb-entry-' . Core\Registry::get('Db')->lastInsertId();
					$body = sprintf($settings['notify'] == 1 ? Core\Registry::get('Lang')->t('guestbook', 'notification_email_body_1') : Core\Registry::get('Lang')->t('guestbook', 'notification_email_body_2'), $host, $fullPath);
					Core\Functions::generateEmail('', $settings['notify_email'], $settings['notify_email'], Core\Registry::get('Lang')->t('guestbook', 'notification_email_subject'), $body);
				}

				// Falls es der Benutzer ausgewählt hat, diesen in den Newsletter eintragen
				if ($newsletterAccess === true && isset($_POST['subscribe_newsletter']) && $_POST['subscribe_newsletter'] == 1) {
					\ACP3\Modules\Newsletter\NewsletterHelpers::subscribeToNewsletter($_POST['mail']);
				}

				Core\Registry::get('Session')->unsetFormToken();

				Core\Functions::setRedirectMessage($bool, Core\Registry::get('Lang')->t('system', $bool !== false ? 'create_success' : 'create_error'), 'guestbook', (bool) $overlay_active);
			}
		}
		if (isset($_POST['submit']) === false || isset($errors) === true && is_array($errors) === true) {
			// Emoticons einbinden
			if ($settings['emoticons'] == 1 && Core\Modules::isActive('emoticons') === true) {
				Core\Registry::get('View')->assign('emoticons', \ACP3\Modules\Emoticons\EmoticonsHelpers::emoticonsList());
			}

			// In Newsletter integrieren
			if ($newsletterAccess === true) {
				Core\Registry::get('View')->assign('subscribe_newsletter', Core\Functions::selectEntry('subscribe_newsletter', '1', '1', 'checked'));
				Core\Registry::get('View')->assign('LANG_subscribe_to_newsletter', sprintf(Core\Registry::get('Lang')->t('guestbook', 'subscribe_to_newsletter'), CONFIG_SEO_TITLE));
			}

			// Falls Benutzer eingeloggt ist, Formular schon teilweise ausfüllen
			if (Core\Registry::get('Auth')->isUser() === true) {
				$user = Core\Registry::get('Auth')->getUserInfo();
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
				Core\Registry::get('View')->assign('form', isset($_POST['submit']) ? $_POST : $user);
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

				Core\Registry::get('View')->assign('form', isset($_POST['submit']) ? array_merge($defaults, $_POST) : $defaults);
			}

			if ($captchaAccess === true) {
				Core\Registry::get('View')->assign('captcha', \ACP3\Modules\Captcha\CaptchaHelpers::captcha());
			}

			Core\Registry::get('Session')->generateFormToken();
		}
	}

	public function actionList() {
		Core\Functions::getRedirectMessage();

		$settings = Core\Config::getSettings('guestbook');
		Core\Registry::get('View')->assign('overlay', $settings['overlay']);

		$guestbook = Core\Registry::get('Db')->fetchAll('SELECT g.user_id, u.id AS user_id_real, u.nickname AS user_name, u.website AS user_website, u.mail AS user_mail, g.id, g.date, g.name, g.message, g.website, g.mail FROM ' . DB_PRE . 'guestbook AS g LEFT JOIN ' . DB_PRE . 'users AS u ON(u.id = g.user_id) ' . ($settings['notify'] == 2 ? 'WHERE active = 1' : '') . ' ORDER BY date DESC LIMIT ' . POS . ',' . Core\Registry::get('Auth')->entries);
		$c_guestbook = count($guestbook);

		if ($c_guestbook > 0) {
			Core\Registry::get('View')->assign('pagination', Core\Functions::pagination(Core\Registry::get('Db')->fetchColumn('SELECT COUNT(*) FROM ' . DB_PRE . 'guestbook')));

			// Emoticons einbinden
			$emoticons_active = false;
			if ($settings['emoticons'] == 1) {
				$emoticons_active = Core\Modules::isActive('emoticons') === true && $settings['emoticons'] == 1 ? true : false;
			}

			for ($i = 0; $i < $c_guestbook; ++$i) {
				$guestbook[$i]['name'] = !empty($guestbook[$i]['user_name']) ? $guestbook[$i]['user_name'] : $guestbook[$i]['name'];
				$guestbook[$i]['date_formatted'] = Core\Registry::get('Date')->format($guestbook[$i]['date'], $settings['dateformat']);
				$guestbook[$i]['date_iso'] = Core\Registry::get('Date')->format($guestbook[$i]['date'], 'c');
				$guestbook[$i]['message'] = Core\Functions::nl2p($guestbook[$i]['message']);
				if ($emoticons_active === true) {
					$guestbook[$i]['message'] = \ACP3\Modules\Emoticons\EmoticonsHelpers::emoticonsReplace($guestbook[$i]['message']);
				}
				$guestbook[$i]['website'] = strlen($guestbook[$i]['user_website']) > 2 ? substr($guestbook[$i]['user_website'], 0, -2) : $guestbook[$i]['website'];
				if (!empty($guestbook[$i]['website']) && (bool) preg_match('=^http(s)?://=', $guestbook[$i]['website']) === false)
					$guestbook[$i]['website'] = 'http://' . $guestbook[$i]['website'];

				$guestbook[$i]['mail'] = !empty($guestbook[$i]['user_mail']) ? substr($guestbook[$i]['user_mail'], 0, -2) : $guestbook[$i]['mail'];
			}
			Core\Registry::get('View')->assign('guestbook', $guestbook);
		}
	}

}