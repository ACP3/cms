<?php

namespace ACP3\Modules\Contact;

use ACP3\Core;

/**
 * Description of ContactFrontend
 *
 * @author Tino Goratsch
 */
class ContactFrontend extends Core\ModuleController {

	public function actionList()
	{
		$captchaAccess = Core\Modules::hasPermission('captcha', 'image');

		if (isset($_POST['submit']) === true) {
			if (empty($_POST['name']))
				$errors['name'] = Core\Registry::get('Lang')->t('system', 'name_to_short');
			if (Core\Validate::email($_POST['mail']) === false)
				$errors['mail'] = Core\Registry::get('Lang')->t('system', 'wrong_email_format');
			if (strlen($_POST['message']) < 3)
				$errors['message'] = Core\Registry::get('Lang')->t('system', 'message_to_short');
			if ($captchaAccess === true && Core\Registry::get('Auth')->isUser() === false && Core\Validate::captcha($_POST['captcha']) === false)
				$errors['captcha'] = Core\Registry::get('Lang')->t('captcha', 'invalid_captcha_entered');

			if (isset($errors) === true) {
				Core\Registry::get('View')->assign('error_msg', Core\Functions::errorBox($errors));
			} elseif (Core\Validate::formToken() === false) {
				Core\Registry::get('View')->setContent(Core\Functions::errorBox(Core\Registry::get('Lang')->t('system', 'form_already_submitted')));
			} else {
				$settings = Core\Config::getSettings('contact');
				$_POST['message'] = Core\Functions::strEncode($_POST['message'], true);

				$subject = sprintf(Core\Registry::get('Lang')->t('contact', 'contact_subject'), CONFIG_SEO_TITLE);
				$body = str_replace(array('{name}', '{mail}', '{message}', '\n'), array($_POST['name'], $_POST['mail'], $_POST['message'], "\n"), Core\Registry::get('Lang')->t('contact', 'contact_body'));
				$bool = Core\Functions::generateEmail('', $settings['mail'], $_POST['mail'], $subject, $body);

				// Nachrichtenkopie an Absender senden
				if (isset($_POST['copy'])) {
					$subject2 = sprintf(Core\Registry::get('Lang')->t('contact', 'sender_subject'), CONFIG_SEO_TITLE);
					$body2 = sprintf(Core\Registry::get('Lang')->t('contact', 'sender_body'), CONFIG_SEO_TITLE, $_POST['message']);
					Core\Functions::generateEmail($_POST['name'], $_POST['mail'], $settings['mail'], $subject2, $body2);
				}

				Core\Registry::get('Session')->unsetFormToken();

				Core\Registry::get('View')->setContent(Core\Functions::confirmBox($bool === true ? Core\Registry::get('Lang')->t('contact', 'send_mail_success') : Core\Registry::get('Lang')->t('contact', 'send_mail_error'), Core\Registry::get('URI')->route('contact')));
			}
		}
		if (isset($_POST['submit']) === false || isset($errors) === true && is_array($errors) === true) {
			// Falls Benutzer eingeloggt ist, Formular schon teilweise ausfüllen
			if (Core\Registry::get('Auth')->isUser() === true) {
				$defaults = Core\Registry::get('Auth')->getUserInfo();
				$disabled = ' readonly="readonly" class="readonly"';
				$defaults['name'] = !empty($defaults['realname']) ? $defaults['realname'] : $defaults['nickname'];
				$defaults['message'] = '';

				if (isset($_POST['submit'])) {
					$_POST['name_disabled'] = $disabled;
					$_POST['mail_disabled'] = $disabled;
				} else {
					$defaults['name_disabled'] = $disabled;
					$defaults['mail_disabled'] = $disabled;
				}
			} else {
				$defaults = array(
					'name' => '',
					'name_disabled' => '',
					'mail' => '',
					'mail_disabled' => '',
					'message' => '',
				);
			}
			Core\Registry::get('View')->assign('form', isset($_POST['submit']) ? array_merge($defaults, $_POST) : $defaults);
			Core\Registry::get('View')->assign('copy_checked', Core\Functions::selectEntry('copy', 1, 0, 'checked'));

			if ($captchaAccess === true) {
				Core\Registry::get('View')->assign('captcha', \ACP3\Modules\Captcha\CaptchaFunctions::captcha());
			}

			Core\Registry::get('Session')->generateFormToken();
		}
	}

	public function actionImprint()
	{
		$settings = Core\Config::getSettings('contact');
		$settings['address'] = Core\Functions::rewriteInternalUri($settings['address']);
		$settings['disclaimer'] = Core\Functions::rewriteInternalUri($settings['disclaimer']);
		Core\Registry::get('View')->assign('imprint', $settings);

		Core\Registry::get('View')->assign('powered_by', sprintf(Core\Registry::get('Lang')->t('contact', 'powered_by'), '<a href="http://www.acp3-cms.net" onclick="window.open(this.href); return false">ACP3</a>'));
	}

	public function actionSidebar()
	{
		$settings = Core\Config::getSettings('contact');
		$settings['address'] = Core\Functions::rewriteInternalUri($settings['address']);
		$settings['disclaimer'] = Core\Functions::rewriteInternalUri($settings['disclaimer']);
		Core\Registry::get('View')->assign('sidebar_contact', $settings);

		Core\Registry::get('View')->displayTemplate('contact/sidebar.tpl');
	}

}