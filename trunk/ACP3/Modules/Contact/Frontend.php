<?php

namespace ACP3\Modules\Contact;

use ACP3\Core;

/**
 * Description of ContactFrontend
 *
 * @author Tino Goratsch
 */
class Frontend extends Core\Modules\Controller {

	public function __construct() {
		parent::__construct();
	}

	public function actionList()
	{
		$captchaAccess = Core\Modules::hasPermission('captcha', 'image');

		if (isset($_POST['submit']) === true) {
			if (empty($_POST['name']))
				$errors['name'] = $this->lang->t('system', 'name_to_short');
			if (Core\Validate::email($_POST['mail']) === false)
				$errors['mail'] = $this->lang->t('system', 'wrong_email_format');
			if (strlen($_POST['message']) < 3)
				$errors['message'] = $this->lang->t('system', 'message_to_short');
			if ($captchaAccess === true && $this->auth->isUser() === false && Core\Validate::captcha($_POST['captcha']) === false)
				$errors['captcha'] = $this->lang->t('captcha', 'invalid_captcha_entered');

			if (isset($errors) === true) {
				$this->view->assign('error_msg', Core\Functions::errorBox($errors));
			} elseif (Core\Validate::formToken() === false) {
				$this->view->setContent(Core\Functions::errorBox($this->lang->t('system', 'form_already_submitted')));
			} else {
				$settings = Core\Config::getSettings('contact');
				$_POST['message'] = Core\Functions::strEncode($_POST['message'], true);

				$subject = sprintf($this->lang->t('contact', 'contact_subject'), CONFIG_SEO_TITLE);
				$body = str_replace(array('{name}', '{mail}', '{message}', '\n'), array($_POST['name'], $_POST['mail'], $_POST['message'], "\n"), $this->lang->t('contact', 'contact_body'));
				$bool = Core\Functions::generateEmail('', $settings['mail'], $_POST['mail'], $subject, $body);

				// Nachrichtenkopie an Absender senden
				if (isset($_POST['copy'])) {
					$subject2 = sprintf($this->lang->t('contact', 'sender_subject'), CONFIG_SEO_TITLE);
					$body2 = sprintf($this->lang->t('contact', 'sender_body'), CONFIG_SEO_TITLE, $_POST['message']);
					Core\Functions::generateEmail($_POST['name'], $_POST['mail'], $settings['mail'], $subject2, $body2);
				}

				$this->session->unsetFormToken();

				$this->view->setContent(Core\Functions::confirmBox($bool === true ? $this->lang->t('contact', 'send_mail_success') : $this->lang->t('contact', 'send_mail_error'), $this->uri->route('contact')));
			}
		}
		if (isset($_POST['submit']) === false || isset($errors) === true && is_array($errors) === true) {
			// Falls Benutzer eingeloggt ist, Formular schon teilweise ausfüllen
			if ($this->auth->isUser() === true) {
				$defaults = $this->auth->getUserInfo();
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
			$this->view->assign('form', isset($_POST['submit']) ? array_merge($defaults, $_POST) : $defaults);
			$this->view->assign('copy_checked', Core\Functions::selectEntry('copy', 1, 0, 'checked'));

			if ($captchaAccess === true) {
				$this->view->assign('captcha', \ACP3\Modules\Captcha\Helpers::captcha());
			}

			$this->session->generateFormToken();
		}
	}

	public function actionImprint()
	{
		$settings = Core\Config::getSettings('contact');
		$settings['address'] = Core\Functions::rewriteInternalUri($settings['address']);
		$settings['disclaimer'] = Core\Functions::rewriteInternalUri($settings['disclaimer']);
		$this->view->assign('imprint', $settings);

		$this->view->assign('powered_by', sprintf($this->lang->t('contact', 'powered_by'), '<a href="http://www.acp3-cms.net" onclick="window.open(this.href); return false">ACP3</a>'));
	}

	public function actionSidebar()
	{
		$settings = Core\Config::getSettings('contact');
		$settings['address'] = Core\Functions::rewriteInternalUri($settings['address']);
		$settings['disclaimer'] = Core\Functions::rewriteInternalUri($settings['disclaimer']);
		$this->view->assign('sidebar_contact', $settings);

		$this->view->displayTemplate('contact/sidebar.tpl');
	}

}