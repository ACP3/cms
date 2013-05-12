<?php

namespace ACP3\Modules\Newsletter;

use ACP3\Core;

/**
 * Description of NewsletterFrontend
 *
 * @author Tino
 */
class NewsletterFrontend extends Core\ModuleController {

	public function __construct($injector) {
		parent::__construct($injector);
	}

	public function actionActivate() {
		if (Core\Validate::email($this->injector['URI']->mail) && Core\Validate::isMD5($this->injector['URI']->hash)) {
			$mail = $this->injector['URI']->mail;
			$hash = $this->injector['URI']->hash;
		} else {
			$this->injector['URI']->redirect('errors/404');
		}

		if ($this->injector['Db']->fetchColumn('SELECT COUNT(*) FROM ' . DB_PRE . 'newsletter_accounts WHERE mail = ? AND has = ?', array($mail, $hash)) != 1)
			$errors[] = $this->injector['Lang']->t('newsletter', 'account_not_exists');

		if (isset($errors) === true) {
			$this->injector['View']->setContent(Core\Functions::errorBox($errors));
		} else {
			$bool = $this->injector['Db']->update(DB_PRE . 'newsletter_accounts', array('hash' => ''), array('mail' => $mail, 'hash' => $hash));

			$this->injector['View']->setContent(Core\Functions::confirmBox($this->injector['Lang']->t('newsletter', $bool !== false ? 'activate_success' : 'activate_error'), ROOT_DIR));
		}
	}

	public function actionArchive() {
		$this->injector['Breadcrumb']->append($this->injector['Lang']->t('newsletter', 'archive'));

		if (isset($_POST['newsletter']) === true &&
				Core\Validate::isNumber($_POST['newsletter'])) {
			$id = (int) $_POST['newsletter'];

			$newsletter = $this->injector['Db']->fetchAssoc('SELECT date, title, text FROM ' . DB_PRE . 'newsletters WHERE id = ? AND status = ?', array($id, 1));
			if (!empty($newsletter)) {
				$newsletter['date_formatted'] = $this->injector['Date']->format($newsletter['date'], 'short');
				$newsletter['date_iso'] = $this->injector['Date']->format($newsletter['date'], 'c');
				$newsletter['text'] = Core\Functions::nl2p($newsletter['text']);

				$this->injector['View']->assign('newsletter', $newsletter);
			}
		}

		$newsletters = $this->injector['Db']->fetchAll('SELECT id, date, title FROM ' . DB_PRE . 'newsletters WHERE status = ? ORDER BY date DESC', array(1));
		$c_newsletters = count($newsletters);

		if ($c_newsletters > 0) {
			for ($i = 0; $i < $c_newsletters; ++$i) {
				$newsletters[$i]['date_formatted'] = $this->injector['Date']->format($newsletters[$i]['date'], 'short');
				$newsletters[$i]['selected'] = Core\Functions::selectEntry('newsletter', $newsletters[$i]['id']);
			}
			$this->injector['View']->assign('newsletters', $newsletters);
		}
	}

	public function actionList() {
		$captchaAccess = Core\Modules::isActive('captcha');

		if (isset($_POST['submit']) === true) {
			switch ($this->injector['URI']->action) {
				case 'subscribe':
					if (Core\Validate::email($_POST['mail']) === false)
						$errors['mail'] = $this->injector['Lang']->t('system', 'wrong_email_format');
					if (Core\Validate::email($_POST['mail']) && $this->injector['Db']->fetchColumn('SELECT COUNT(*) FROM ' . DB_PRE . 'newsletter_accounts WHERE mail = ?', array($_POST['mail'])) == 1)
						$errors['mail'] = $this->injector['Lang']->t('newsletter', 'account_exists');
					if ($captchaAccess === true && $this->injector['Auth']->isUser() === false && Core\Validate::captcha($_POST['captcha']) === false)
						$errors['captcha'] = $this->injector['Lang']->t('captcha', 'invalid_captcha_entered');

					if (isset($errors) === true) {
						$this->injector['View']->assign('error_msg', Core\Functions::errorBox($errors));
					} elseif (Core\Validate::formToken() === false) {
						$this->injector['View']->setContent(Core\Functions::errorBox($this->injector['Lang']->t('system', 'form_already_submitted')));
					} else {
						$bool = NewsletterFunctions::subscribeToNewsletter($_POST['mail']);

						$this->injector['Session']->unsetFormToken();

						$this->injector['View']->setContent(Core\Functions::confirmBox($this->injector['Lang']->t('newsletter', $bool !== false ? 'subscribe_success' : 'subscribe_error'), ROOT_DIR));
					}
					break;
				case 'unsubscribe':
					if (Core\Validate::email($_POST['mail']) === false)
						$errors[] = $this->injector['Lang']->t('system', 'wrong_email_format');
					if (Core\Validate::email($_POST['mail']) && $this->injector['Db']->fetchColumn('SELECT COUNT(*) FROM ' . DB_PRE . 'newsletter_accounts WHERE mail = ?', array($_POST['mail'])) != 1)
						$errors[] = $this->injector['Lang']->t('newsletter', 'account_not_exists');
					if ($captchaAccess === true && $this->injector['Auth']->isUser() === false && Core\Validate::captcha($_POST['captcha']) === false)
						$errors[] = $this->injector['Lang']->t('captcha', 'invalid_captcha_entered');

					if (isset($errors) === true) {
						$this->injector['View']->assign('error_msg', Core\Functions::errorBox($errors));
					} elseif (Core\Validate::formToken() === false) {
						$this->injector['View']->setContent(Core\Functions::errorBox($this->injector['Lang']->t('system', 'form_already_submitted')));
					} else {
						$bool = $this->injector['Db']->delete(DB_PRE . 'newsletter_accounts', array('mail' => $_POST['mail']));

						$this->injector['Session']->unsetFormToken();

						$this->injector['View']->setContent(Core\Functions::confirmBox($this->injector['Lang']->t('newsletter', $bool !== false ? 'unsubscribe_success' : 'unsubscribe_error'), ROOT_DIR));
					}
					break;
				default:
					$this->injector['URI']->redirect('errors/404');
			}
		}
		if (isset($_POST['submit']) === false || isset($errors) === true && is_array($errors) === true) {
			$this->injector['View']->assign('form', isset($_POST['submit']) ? $_POST : array('mail' => ''));

			$field_value = $this->injector['URI']->action ? $this->injector['URI']->action : 'subscribe';

			$actions_Lang = array(
				$this->injector['Lang']->t('newsletter', 'subscribe'),
				$this->injector['Lang']->t('newsletter', 'unsubscribe')
			);
			$this->injector['View']->assign('actions', Core\Functions::selectGenerator('action', array('subscribe', 'unsubstribe'), $actions_Lang, $field_value, 'checked'));

			if ($captchaAccess === true) {
				$this->injector['View']->assign('captcha', \ACP3\Modules\Captcha\CaptchaFunctions::captcha());
			}

			$this->injector['Session']->generateFormToken();
		}
	}

	public function actionSidebar() {
		if (Core\Modules::isActive('captcha') === true) {
			$this->injector['View']->assign('captcha', \ACP3\Modules\Captcha\CaptchaFunctions::captcha());
		}

		$this->injector['Session']->generateFormToken('newsletter/list');

		$this->injector['View']->displayTemplate('newsletter/sidebar.tpl');
	}

}