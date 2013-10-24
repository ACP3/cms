<?php

namespace ACP3\Modules\Guestbook;

use ACP3\Core;

/**
 * Description of GuestbookFrontend
 *
 * @author Tino Goratsch
 */
class Frontend extends Core\ModuleController {

	public function __construct() {
		parent::__construct();
	}

	public function actionCreate() {
		$this->breadcrumb
				->append($this->lang->t('guestbook', 'guestbook'), $this->uri->route('guestbook'))
				->append($this->lang->t('guestbook', 'create'));

		$settings = Core\Config::getSettings('guestbook');
		$newsletterAccess = Core\Modules::hasPermission('newsletter', 'list') === true && $settings['newsletter_integration'] == 1;
		$captchaAccess = Core\Modules::hasPermission('captcha', 'image');

		$overlay_active = false;
		if ($this->uri->layout === 'simple') {
			$overlay_active = true;
			$this->view->setLayout('simple.tpl');
		}

		if (isset($_POST['submit']) === true) {
			$ip = $_SERVER['REMOTE_ADDR'];

			// Flood Sperre
			$flood = $this->db->fetchColumn('SELECT MAX(date) FROM ' . DB_PRE . 'guestbook WHERE ip = ?', array($ip));
			if (!empty($flood)) {
				$flood_time = $this->date->timestamp($flood) + CONFIG_FLOOD;
			}
			$time = $this->date->timestamp();

			if (isset($flood_time) && $flood_time > $time)
				$errors[] = sprintf($this->lang->t('system', 'flood_no_entry_possible'), $flood_time - $time);
			if (empty($_POST['name']))
				$errors['name'] = $this->lang->t('system', 'name_to_short');
			if (!empty($_POST['mail']) && Core\Validate::email($_POST['mail']) === false)
				$errors['mail'] = $this->lang->t('system', 'wrong_email_format');
			if (strlen($_POST['message']) < 3)
				$errors['message'] = $this->lang->t('system', 'message_to_short');
			if ($captchaAccess === true && $this->auth->isUser() === false && Core\Validate::captcha($_POST['captcha']) === false)
				$errors['captcha'] = $this->lang->t('captcha', 'invalid_captcha_entered');
			if ($newsletterAccess === true && isset($_POST['subscribe_newsletter']) && $_POST['subscribe_newsletter'] == 1) {
				if (Core\Validate::email($_POST['mail']) === false)
					$errors['mail'] = $this->lang->t('guestbook', 'type_in_email_address_to_subscribe_to_newsletter');
				if (Core\Validate::email($_POST['mail']) === true &&
						$this->db->fetchColumn('SELECT COUNT(*) FROM ' . DB_PRE . 'newsletter_accounts WHERE mail = ?', array($_POST['mail'])) == 1)
					$errors[] = $this->lang->t('newsletter', 'account_exists');
			}

			if (isset($errors) === true) {
				$this->view->assign('error_msg', Core\Functions::errorBox($errors));
			} elseif (Core\Validate::formToken() === false) {
				$this->view->setContent(Core\Functions::errorBox($this->lang->t('system', 'form_already_submitted')));
			} else {
				$insert_values = array(
					'id' => '',
					'date' => $this->date->getCurrentDateTime(),
					'ip' => $ip,
					'name' => Core\Functions::strEncode($_POST['name']),
					'user_id' => $this->auth->isUser() ? $this->auth->getUserId() : '',
					'message' => Core\Functions::strEncode($_POST['message']),
					'website' => Core\Functions::strEncode($_POST['website']),
					'mail' => $_POST['mail'],
					'active' => $settings['notify'] == 2 ? 0 : 1,
				);

				$bool = $this->db->insert(DB_PRE . 'guestbook', $insert_values);

				// E-Mail-Benachrichtigung bei neuem Eintrag der hinterlegten
				// E-Mail-Adresse zusenden
				if ($settings['notify'] == 1 || $settings['notify'] == 2) {
					$host = 'http://' . htmlentities($_SERVER['HTTP_HOST']);
					$fullPath = $host . $this->uri->route('guestbook/list') . '#gb-entry-' . $this->db->lastInsertId();
					$body = sprintf($settings['notify'] == 1 ? $this->lang->t('guestbook', 'notification_email_body_1') : $this->lang->t('guestbook', 'notification_email_body_2'), $host, $fullPath);
					Core\Functions::generateEmail('', $settings['notify_email'], $settings['notify_email'], $this->lang->t('guestbook', 'notification_email_subject'), $body);
				}

				// Falls es der Benutzer ausgewählt hat, diesen in den Newsletter eintragen
				if ($newsletterAccess === true && isset($_POST['subscribe_newsletter']) && $_POST['subscribe_newsletter'] == 1) {
					\ACP3\Modules\Newsletter\Helpers::subscribeToNewsletter($_POST['mail']);
				}

				$this->session->unsetFormToken();

				Core\Functions::setRedirectMessage($bool, $this->lang->t('system', $bool !== false ? 'create_success' : 'create_error'), 'guestbook', (bool) $overlay_active);
			}
		}
		if (isset($_POST['submit']) === false || isset($errors) === true && is_array($errors) === true) {
			// Emoticons einbinden
			if ($settings['emoticons'] == 1 && Core\Modules::isActive('emoticons') === true) {
				$this->view->assign('emoticons', \ACP3\Modules\Emoticons\Helpers::emoticonsList());
			}

			// In Newsletter integrieren
			if ($newsletterAccess === true) {
				$this->view->assign('subscribe_newsletter', Core\Functions::selectEntry('subscribe_newsletter', '1', '1', 'checked'));
				$this->view->assign('LANG_subscribe_to_newsletter', sprintf($this->lang->t('guestbook', 'subscribe_to_newsletter'), CONFIG_SEO_TITLE));
			}

			// Falls Benutzer eingeloggt ist, Formular schon teilweise ausfüllen
			if ($this->auth->isUser() === true) {
				$user = $this->auth->getUserInfo();
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
				$this->view->assign('form', isset($_POST['submit']) ? $_POST : $user);
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

				$this->view->assign('form', isset($_POST['submit']) ? array_merge($defaults, $_POST) : $defaults);
			}

			if ($captchaAccess === true) {
				$this->view->assign('captcha', \ACP3\Modules\Captcha\Helpers::captcha());
			}

			$this->session->generateFormToken();
		}
	}

	public function actionList() {
		Core\Functions::getRedirectMessage();

		$settings = Core\Config::getSettings('guestbook');
		$this->view->assign('overlay', $settings['overlay']);

		$guestbook = $this->db->fetchAll('SELECT g.user_id, u.id AS user_id_real, u.nickname AS user_name, u.website AS user_website, u.mail AS user_mail, g.id, g.date, g.name, g.message, g.website, g.mail FROM ' . DB_PRE . 'guestbook AS g LEFT JOIN ' . DB_PRE . 'users AS u ON(u.id = g.user_id) ' . ($settings['notify'] == 2 ? 'WHERE active = 1' : '') . ' ORDER BY date DESC LIMIT ' . POS . ',' . $this->auth->entries);
		$c_guestbook = count($guestbook);

		if ($c_guestbook > 0) {
			$this->view->assign('pagination', Core\Functions::pagination($this->db->fetchColumn('SELECT COUNT(*) FROM ' . DB_PRE . 'guestbook')));

			// Emoticons einbinden
			$emoticons_active = false;
			if ($settings['emoticons'] == 1) {
				$emoticons_active = Core\Modules::isActive('emoticons') === true && $settings['emoticons'] == 1 ? true : false;
			}

			for ($i = 0; $i < $c_guestbook; ++$i) {
				$guestbook[$i]['name'] = !empty($guestbook[$i]['user_name']) ? $guestbook[$i]['user_name'] : $guestbook[$i]['name'];
				$guestbook[$i]['date_formatted'] = $this->date->format($guestbook[$i]['date'], $settings['dateformat']);
				$guestbook[$i]['date_iso'] = $this->date->format($guestbook[$i]['date'], 'c');
				$guestbook[$i]['message'] = Core\Functions::nl2p($guestbook[$i]['message']);
				if ($emoticons_active === true) {
					$guestbook[$i]['message'] = \ACP3\Modules\Emoticons\Helpers::emoticonsReplace($guestbook[$i]['message']);
				}
				$guestbook[$i]['website'] = strlen($guestbook[$i]['user_website']) > 2 ? substr($guestbook[$i]['user_website'], 0, -2) : $guestbook[$i]['website'];
				if (!empty($guestbook[$i]['website']) && (bool) preg_match('=^http(s)?://=', $guestbook[$i]['website']) === false)
					$guestbook[$i]['website'] = 'http://' . $guestbook[$i]['website'];

				$guestbook[$i]['mail'] = !empty($guestbook[$i]['user_mail']) ? substr($guestbook[$i]['user_mail'], 0, -2) : $guestbook[$i]['mail'];
			}
			$this->view->assign('guestbook', $guestbook);
		}
	}

}