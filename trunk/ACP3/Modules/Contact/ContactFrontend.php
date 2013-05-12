<?php

namespace ACP3\Modules\Contact;

use ACP3\Core;

/**
 * Description of ContactFrontend
 *
 * @author Tino
 */
class ContactFrontend extends Core\ModuleController {

	public function __construct($injector)
	{
		parent::__construct($injector);
	}

	public function actionList()
	{
		$captchaAccess = Core\Modules::check('captcha', 'image');

		if (isset($_POST['submit']) === true) {
			if (empty($_POST['name']))
				$errors['name'] = $this->injector['Lang']->t('system', 'name_to_short');
			if (Core\Validate::email($_POST['mail']) === false)
				$errors['mail'] = $this->injector['Lang']->t('system', 'wrong_email_format');
			if (strlen($_POST['message']) < 3)
				$errors['message'] = $this->injector['Lang']->t('system', 'message_to_short');
			if ($captchaAccess === true && $this->injector['Auth']->isUser() === false && Core\Validate::captcha($_POST['captcha']) === false)
				$errors['captcha'] = $this->injector['Lang']->t('captcha', 'invalid_captcha_entered');

			if (isset($errors) === true) {
				$this->injector['View']->assign('error_msg', Core\Functions::errorBox($errors));
			} elseif (Core\Validate::formToken() === false) {
				$this->injector['View']->setContent(Core\Functions::errorBox($this->injector['Lang']->t('system', 'form_already_submitted')));
			} else {
				$settings = Core\Config::getSettings('contact');
				$_POST['message'] = Core\Functions::str_encode($_POST['message'], true);

				$subject = sprintf($this->injector['Lang']->t('contact', 'contact_subject'), CONFIG_SEO_TITLE);
				$body = str_replace(array('{name}', '{mail}', '{message}', '\n'), array($_POST['name'], $_POST['mail'], $_POST['message'], "\n"), $this->injector['Lang']->t('contact', 'contact_body'));
				$bool = Core\Functions::generateEmail('', $settings['mail'], $_POST['mail'], $subject, $body);

				// Nachrichtenkopie an Absender senden
				if (isset($_POST['copy'])) {
					$subject2 = sprintf($this->injector['Lang']->t('contact', 'sender_subject'), CONFIG_SEO_TITLE);
					$body2 = sprintf($this->injector['Lang']->t('contact', 'sender_body'), CONFIG_SEO_TITLE, $_POST['message']);
					Core\Functions::generateEmail($_POST['name'], $_POST['mail'], $settings['mail'], $subject2, $body2);
				}

				$this->injector['Session']->unsetFormToken();

				$this->injector['View']->setContent(Core\Functions::confirmBox($bool === true ? $this->injector['Lang']->t('contact', 'send_mail_success') : $this->injector['Lang']->t('contact', 'send_mail_error'), $this->injector['URI']->route('contact')));
			}
		}
		if (isset($_POST['submit']) === false || isset($errors) === true && is_array($errors) === true) {
			// Falls Benutzer eingeloggt ist, Formular schon teilweise ausfüllen
			if ($this->injector['Auth']->isUser() === true) {
				$defaults = $this->injector['Auth']->getUserInfo();
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
			$this->injector['View']->assign('form', isset($_POST['submit']) ? array_merge($defaults, $_POST) : $defaults);
			$this->injector['View']->assign('copy_checked', Core\Functions::selectEntry('copy', 1, 0, 'checked'));

			if ($captchaAccess === true) {
				$this->injector['View']->assign('captcha', \ACP3\Modules\Captcha\CaptchaFunctions::captcha());
			}

			$this->injector['Session']->generateFormToken();
		}
	}

	public function actionImprint()
	{
		$settings = Core\Config::getSettings('contact');
		$settings['address'] = Core\Functions::rewriteInternalUri($settings['address']);
		$settings['disclaimer'] = Core\Functions::rewriteInternalUri($settings['disclaimer']);
		$this->injector['View']->assign('imprint', $settings);

		$this->injector['View']->assign('powered_by', sprintf($this->injector['Lang']->t('contact', 'powered_by'), '<a href="http://www.acp3-cms.net" onclick="window.open(this.href); return false">ACP3</a>'));
	}

	public function actionSidebar()
	{
		$settings = Core\Config::getSettings('contact');
		$settings['address'] = Core\Functions::rewriteInternalUri($settings['address']);
		$settings['disclaimer'] = Core\Functions::rewriteInternalUri($settings['disclaimer']);
		$this->injector['View']->assign('sidebar_contact', $settings);

		$this->injector['View']->displayTemplate('contact/sidebar.tpl');
	}

}