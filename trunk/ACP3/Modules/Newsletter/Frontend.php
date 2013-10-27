<?php

namespace ACP3\Modules\Newsletter;

use ACP3\Core;

/**
 * Description of NewsletterFrontend
 *
 * @author Tino Goratsch
 */
class Frontend extends Core\Modules\Controller {

	public function __construct() {
		parent::__construct();
	}

	public function actionActivate() {
		if (Core\Validate::email($this->uri->mail) && Core\Validate::isMD5($this->uri->hash)) {
			$mail = $this->uri->mail;
			$hash = $this->uri->hash;
		} else {
			$this->uri->redirect('errors/404');
		}

		if ($this->db->fetchColumn('SELECT COUNT(*) FROM ' . DB_PRE . 'newsletter_accounts WHERE mail = ? AND has = ?', array($mail, $hash)) != 1)
			$errors[] = $this->lang->t('newsletter', 'account_not_exists');

		if (isset($errors) === true) {
			$this->view->setContent(Core\Functions::errorBox($errors));
		} else {
			$bool = $this->db->update(DB_PRE . 'newsletter_accounts', array('hash' => ''), array('mail' => $mail, 'hash' => $hash));

			$this->view->setContent(Core\Functions::confirmBox($this->lang->t('newsletter', $bool !== false ? 'activate_success' : 'activate_error'), ROOT_DIR));
		}
	}

	public function actionArchive() {
		$this->breadcrumb->append($this->lang->t('newsletter', 'archive'));

		if (isset($_POST['newsletter']) === true &&
				Core\Validate::isNumber($_POST['newsletter'])) {
			$id = (int) $_POST['newsletter'];

			$newsletter = $this->db->fetchAssoc('SELECT date, title, text FROM ' . DB_PRE . 'newsletters WHERE id = ? AND status = ?', array($id, 1));
			if (!empty($newsletter)) {
				$newsletter['date_formatted'] = $this->date->format($newsletter['date'], 'short');
				$newsletter['date_iso'] = $this->date->format($newsletter['date'], 'c');
				$newsletter['text'] = Core\Functions::nl2p($newsletter['text']);

				$this->view->assign('newsletter', $newsletter);
			}
		}

		$newsletters = $this->db->fetchAll('SELECT id, date, title FROM ' . DB_PRE . 'newsletters WHERE status = ? ORDER BY date DESC', array(1));
		$c_newsletters = count($newsletters);

		if ($c_newsletters > 0) {
			for ($i = 0; $i < $c_newsletters; ++$i) {
				$newsletters[$i]['date_formatted'] = $this->date->format($newsletters[$i]['date'], 'short');
				$newsletters[$i]['selected'] = Core\Functions::selectEntry('newsletter', $newsletters[$i]['id']);
			}
			$this->view->assign('newsletters', $newsletters);
		}
	}

	public function actionList() {
		$captchaAccess = Core\Modules::hasPermission('captcha', 'image');

		if (isset($_POST['submit']) === true) {
			switch ($this->uri->action) {
				case 'subscribe':
					if (Core\Validate::email($_POST['mail']) === false)
						$errors['mail'] = $this->lang->t('system', 'wrong_email_format');
					if (Core\Validate::email($_POST['mail']) && $this->db->fetchColumn('SELECT COUNT(*) FROM ' . DB_PRE . 'newsletter_accounts WHERE mail = ?', array($_POST['mail'])) == 1)
						$errors['mail'] = $this->lang->t('newsletter', 'account_exists');
					if ($captchaAccess === true && $this->auth->isUser() === false && Core\Validate::captcha($_POST['captcha']) === false)
						$errors['captcha'] = $this->lang->t('captcha', 'invalid_captcha_entered');

					if (isset($errors) === true) {
						$this->view->assign('error_msg', Core\Functions::errorBox($errors));
					} elseif (Core\Validate::formToken() === false) {
						$this->view->setContent(Core\Functions::errorBox($this->lang->t('system', 'form_already_submitted')));
					} else {
						$bool = Helpers::subscribeToNewsletter($_POST['mail']);

						$this->session->unsetFormToken();

						$this->view->setContent(Core\Functions::confirmBox($this->lang->t('newsletter', $bool !== false ? 'subscribe_success' : 'subscribe_error'), ROOT_DIR));
					}
					break;
				case 'unsubscribe':
					if (Core\Validate::email($_POST['mail']) === false)
						$errors[] = $this->lang->t('system', 'wrong_email_format');
					if (Core\Validate::email($_POST['mail']) && $this->db->fetchColumn('SELECT COUNT(*) FROM ' . DB_PRE . 'newsletter_accounts WHERE mail = ?', array($_POST['mail'])) != 1)
						$errors[] = $this->lang->t('newsletter', 'account_not_exists');
					if ($captchaAccess === true && $this->auth->isUser() === false && Core\Validate::captcha($_POST['captcha']) === false)
						$errors[] = $this->lang->t('captcha', 'invalid_captcha_entered');

					if (isset($errors) === true) {
						$this->view->assign('error_msg', Core\Functions::errorBox($errors));
					} elseif (Core\Validate::formToken() === false) {
						$this->view->setContent(Core\Functions::errorBox($this->lang->t('system', 'form_already_submitted')));
					} else {
						$bool = $this->db->delete(DB_PRE . 'newsletter_accounts', array('mail' => $_POST['mail']));

						$this->session->unsetFormToken();

						$this->view->setContent(Core\Functions::confirmBox($this->lang->t('newsletter', $bool !== false ? 'unsubscribe_success' : 'unsubscribe_error'), ROOT_DIR));
					}
					break;
				default:
					$this->uri->redirect('errors/404');
			}
		}
		if (isset($_POST['submit']) === false || isset($errors) === true && is_array($errors) === true) {
			$this->view->assign('form', isset($_POST['submit']) ? $_POST : array('mail' => ''));

			$field_value = $this->uri->action ? $this->uri->action : 'subscribe';

			$actions_Lang = array(
				$this->lang->t('newsletter', 'subscribe'),
				$this->lang->t('newsletter', 'unsubscribe')
			);
			$this->view->assign('actions', Core\Functions::selectGenerator('action', array('subscribe', 'unsubstribe'), $actions_Lang, $field_value, 'checked'));

			if ($captchaAccess === true) {
				$this->view->assign('captcha', \ACP3\Modules\Captcha\Helpers::captcha());
			}

			$this->session->generateFormToken();
		}
	}

	public function actionSidebar() {
		if (Core\Modules::hasPermission('captcha', 'image') === true) {
			$this->view->assign('captcha', \ACP3\Modules\Captcha\Helpers::captcha(3, 'captcha', true, 'newsletter'));
		}

		$this->session->generateFormToken('newsletter/list');

		$this->view->displayTemplate('newsletter/sidebar.tpl');
	}

}