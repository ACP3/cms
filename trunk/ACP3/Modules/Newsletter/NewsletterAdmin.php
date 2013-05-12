<?php

namespace ACP3\Modules\Newsletter;

use ACP3\Core;

/**
 * Description of NewsletterAdmin
 *
 * @author Tino
 */
class NewsletterAdmin extends Core\ModuleController {

	public function __construct($injector) {
		parent::__construct($injector);
	}

	public function actionActivate() {
		$bool = false;
		if (Core\Validate::isNumber($this->injector['URI']->id) === true) {
			$bool = $this->injector['Db']->update(DB_PRE . 'newsletter_accounts', array('hash' => ''), array('id' => $this->injector['URI']->id));
		}

		Core\Functions::setRedirectMessage($bool, $this->injector['Lang']->t('newsletter', $bool !== false ? 'activate_success' : 'activate_error'), 'acp/newsletter');
	}

	public function actionCreate() {
		if (isset($_POST['submit']) === true) {
			if (strlen($_POST['title']) < 3)
				$errors['title'] = $this->injector['Lang']->t('newsletter', 'subject_to_short');
			if (strlen($_POST['text']) < 3)
				$errors['text'] = $this->injector['Lang']->t('newsletter', 'text_to_short');

			if (isset($errors) === true) {
				$this->injector['View']->assign('error_msg', Core\Functions::errorBox($errors));
			} elseif (Core\Validate::formToken() === false) {
				$this->injector['View']->setContent(Core\Functions::errorBox($this->injector['Lang']->t('system', 'form_already_submitted')));
			} else {
				$settings = Core\Config::getSettings('newsletter');

				// Newsletter archivieren
				$insert_values = array(
					'id' => '',
					'date' => $this->injector['Date']->getCurrentDateTime(),
					'title' => Core\Functions::str_encode($_POST['title']),
					'text' => Core\Functions::str_encode($_POST['text'], true),
					'status' => $_POST['test'] == 1 ? '0' : (int) $_POST['action'],
					'user_id' => $this->injector['Auth']->getUserId(),
				);
				$bool = $this->injector['Db']->insert(DB_PRE . 'newsletters', $insert_values);

				if ($_POST['action'] == 1 && $bool !== false) {
					$subject = Core\Functions::str_encode($_POST['title'], true);
					$body = Core\Functions::str_encode($_POST['text'], true) . "\n-- \n" . html_entity_decode($settings['mailsig'], ENT_QUOTES, 'UTF-8');

					// Testnewsletter
					if ($_POST['test'] == 1) {
						$bool2 = Core\Functions::generateEmail('', $settings['mail'], $settings['mail'], $subject, $body);
						// An alle versenden
					} else {
						$bool2 = NewsletterFunctions::sendNewsletter($subject, $body, $settings['mail']);
					}
				}

				$this->injector['Session']->unsetFormToken();

				if ($_POST['action'] == 0 && $bool !== false) {
					Core\Functions::setRedirectMessage(true, $this->injector['Lang']->t('newsletter', 'save_success'), 'acp/newsletter');
				} elseif ($_POST['action'] == 1 && $bool !== false && $bool2 === true) {
					Core\Functions::setRedirectMessage($bool && $bool2, $this->injector['Lang']->t('newsletter', 'create_success'), 'acp/newsletter');
				} else {
					Core\Functions::setRedirectMessage(false, $this->injector['Lang']->t('newsletter', 'create_save_error'), 'acp/newsletter');
				}
			}
		}
		if (isset($_POST['submit']) === false || isset($errors) === true && is_array($errors) === true) {
			$this->injector['View']->assign('form', isset($_POST['submit']) ? $_POST : array('title' => '', 'text' => ''));

			$lang_test = array($this->injector['Lang']->t('system', 'yes'), $this->injector['Lang']->t('system', 'no'));
			$this->injector['View']->assign('test', Core\Functions::selectGenerator('test', array(1, 0), $lang_test, 0, 'checked'));

			$lang_action = array($this->injector['Lang']->t('newsletter', 'send_and_save'), $this->injector['Lang']->t('newsletter', 'only_save'));
			$this->injector['View']->assign('action', Core\Functions::selectGenerator('action', array(1, 0), $lang_action, 1, 'checked'));

			$this->injector['Session']->generateFormToken();
		}
	}

	public function actionDelete() {
		if (isset($_POST['entries']) && is_array($_POST['entries']) === true)
			$entries = $_POST['entries'];
		elseif (Core\Validate::deleteEntries($this->injector['URI']->entries) === true)
			$entries = $this->injector['URI']->entries;

		if (!isset($entries)) {
			$this->injector['View']->setContent(Core\Functions::errorBox($this->injector['Lang']->t('system', 'no_entries_selected')));
		} elseif (is_array($entries) === true) {
			$marked_entries = implode('|', $entries);
			$this->injector['View']->setContent(Core\Functions::confirmBox($this->injector['Lang']->t('system', 'confirm_delete'), $this->injector['URI']->route('acp/newsletter/delete/entries_' . $marked_entries . '/action_confirmed/'), $this->injector['URI']->route('acp/newsletter')));
		} elseif ($this->injector['URI']->action === 'confirmed') {
			$marked_entries = explode('|', $entries);
			$bool = false;
			foreach ($marked_entries as $entry) {
				$bool = $this->injector['Db']->delete(DB_PRE . 'newsletters', array('id' => $entry));
			}
			Core\Functions::setRedirectMessage($bool, $this->injector['Lang']->t('system', $bool !== false ? 'delete_success' : 'delete_error'), 'acp/newsletter');
		} else {
			$this->injector['URI']->redirect('errors/404');
		}
	}

	public function actionDelete_account() {
		if (isset($_POST['entries']) && is_array($_POST['entries']) === true)
			$entries = $_POST['entries'];
		elseif (Core\Validate::deleteEntries($this->injector['URI']->entries) === true)
			$entries = $this->injector['URI']->entries;

		if (!isset($entries)) {
			$this->injector['View']->setContent(Core\Functions::errorBox($this->injector['Lang']->t('system', 'no_entries_selected')));
		} elseif (is_array($entries) === true) {
			$marked_entries = implode('|', $entries);
			$this->injector['View']->setContent(Core\Functions::confirmBox($this->injector['Lang']->t('system', 'confirm_delete'), $this->injector['URI']->route('acp/newsletter/delete_account/entries_' . $marked_entries . '/action_confirmed/'), $this->injector['URI']->route('acp/newsletter/list_accounts')));
		} elseif ($this->injector['URI']->action === 'confirmed') {
			$marked_entries = explode('|', $entries);
			$bool = false;
			foreach ($marked_entries as $entry) {
				$bool = $this->injector['Db']->delete(DB_PRE . 'newsletter_accounts', array('id' => $entry));
			}
			Core\Functions::setRedirectMessage($bool, $this->injector['Lang']->t('system', $bool !== false ? 'delete_success' : 'delete_error'), 'acp/newsletter/list_accounts');
		} else {
			$this->injector['URI']->redirect('errors/404');
		}
	}

	public function actionEdit() {
		if (Core\Validate::isNumber($this->injector['URI']->id) === true &&
				$this->injector['Db']->fetchColumn('SELECT COUNT(*) FROM ' . DB_PRE . 'newsletters WHERE id = ?', array($this->injector['URI']->id)) == 1) {
			// Brotkrümelspur
			$this->injector['Breadcrumb']
					->append($this->injector['Lang']->t('newsletter', 'newsletter'), $this->injector['URI']->route('acp/newsletter'))
					->append($this->injector['Lang']->t('newsletter', 'acp_edit'));

			if (isset($_POST['submit']) === true) {
				if (strlen($_POST['title']) < 3)
					$errors['title'] = $this->injector['Lang']->t('newsletter', 'subject_to_short');
				if (strlen($_POST['text']) < 3)
					$errors['text'] = $this->injector['Lang']->t('newsletter', 'text_to_short');

				if (isset($errors) === true) {
					$this->injector['View']->assign('error_msg', Core\Functions::errorBox($errors));
				} elseif (Core\Validate::formToken() === false) {
					$this->injector['View']->setContent(Core\Functions::errorBox($this->injector['Lang']->t('system', 'form_already_submitted')));
				} else {
					$settings = Core\Config::getSettings('newsletter');

					// Newsletter archivieren
					$update_values = array(
						'date' => $this->injector['Date']->getCurrentDateTime(),
						'title' => Core\Functions::str_encode($_POST['title']),
						'text' => Core\Functions::str_encode($_POST['text'], true),
						'status' => $_POST['test'] == 1 ? '0' : (int) $_POST['action'],
						'user_id' => $this->injector['Auth']->getUserId(),
					);
					$bool = $this->injector['Db']->update(DB_PRE . 'newsletters', $update_values, array('id' => $this->injector['URI']->id));

					if ($_POST['action'] == 1 && $bool !== false) {
						$subject = Core\Functions::str_encode($_POST['title'], true);
						$body = Core\Functions::str_encode($_POST['text'], true) . "\n" . html_entity_decode($settings['mailsig'], ENT_QUOTES, 'UTF-8');

						// Testnewsletter
						if ($_POST['test'] == 1) {
							$bool2 = Core\Functions::generateEmail('', $settings['mail'], $settings['mail'], $subject, $body);
						// An alle versenden
						} else {
							$bool2 = NewsletterFunctions::sendNewsletter($subject, $body, $settings['mail']);
						}
					}

					$this->injector['Session']->unsetFormToken();

					if ($_POST['action'] == 0 && $bool !== false) {
						Core\Functions::setRedirectMessage(true, $this->injector['Lang']->t('newsletter', 'save_success'), 'acp/newsletter');
					} elseif ($_POST['action'] == 1 && $bool !== false && $bool2 === true) {
						Core\Functions::setRedirectMessage($bool && $bool2, $this->injector['Lang']->t('newsletter', 'create_success'), 'acp/newsletter');
					} else {
						Core\Functions::setRedirectMessage(false, $this->injector['Lang']->t('newsletter', 'create_save_error'), 'acp/newsletter');
					}
				}
			}
			if (isset($_POST['submit']) === false || isset($errors) === true && is_array($errors) === true) {
				$newsletter = $this->injector['Db']->fetchAssoc('SELECT title, text FROM ' . DB_PRE . 'newsletters WHERE id = ?', array($this->injector['URI']->id));

				$this->injector['View']->assign('form', isset($_POST['submit']) ? $_POST : $newsletter);

				$lang_test = array($this->injector['Lang']->t('system', 'yes'), $this->injector['Lang']->t('system', 'no'));
				$this->injector['View']->assign('test', Core\Functions::selectGenerator('test', array(1, 0), $lang_test, 0, 'checked'));

				$lang_action = array($this->injector['Lang']->t('newsletter', 'send_and_save'), $this->injector['Lang']->t('newsletter', 'only_save'));
				$this->injector['View']->assign('action', Core\Functions::selectGenerator('action', array(1, 0), $lang_action, 1, 'checked'));

				$this->injector['Session']->generateFormToken();
			}
		} else {
			$this->injector['URI']->redirect('errors/404');
		}
	}

	public function actionList() {
		Core\Functions::getRedirectMessage();

		$newsletter = $this->injector['Db']->fetchAll('SELECT id, date, title, status FROM ' . DB_PRE . 'newsletters ORDER BY date DESC');
		$c_newsletter = count($newsletter);

		if ($c_newsletter > 0) {
			$can_delete = Core\Modules::check('newsletter', 'acp_delete');
			$config = array(
				'element' => '#acp-table',
				'sort_col' => $can_delete === true ? 1 : 0,
				'sort_dir' => 'desc',
				'hide_col_sort' => $can_delete === true ? 0 : ''
			);
			$this->injector['View']->appendContent(Core\Functions::datatable($config));

			$search = array('0', '1');
			$replace = array($this->injector['Lang']->t('newsletter', 'not_yet_sent'), $this->injector['Lang']->t('newsletter', 'already_sent'));
			for ($i = 0; $i < $c_newsletter; ++$i) {
				$newsletter[$i]['date_formatted'] = $this->injector['Date']->formatTimeRange($newsletter[$i]['date']);
				$newsletter[$i]['status'] = str_replace($search, $replace, $newsletter[$i]['status']);
			}
			$this->injector['View']->assign('newsletter', $newsletter);
			$this->injector['View']->assign('can_delete', $can_delete);
			$this->injector['View']->assign('can_send', Core\Modules::check('newsletter', 'acp_send'));
		}
	}

	public function actionList_accounts() {
		Core\Functions::getRedirectMessage();

		$accounts = $this->injector['Db']->fetchAll('SELECT id, mail, hash FROM ' . DB_PRE . 'newsletter_accounts ORDER BY id DESC');
		$c_accounts = count($accounts);

		if ($c_accounts > 0) {
			$can_delete = Core\Modules::check('newsletter', 'acp_delete_account');
			$config = array(
				'element' => '#acp-table',
				'sort_col' => $can_delete === true ? 3 : 2,
				'sort_dir' => 'desc',
				'hide_col_sort' => $can_delete === true ? 0 : ''
			);
			$this->injector['View']->appendContent(Core\Functions::datatable($config));

			$this->injector['View']->assign('accounts', $accounts);
			$this->injector['View']->assign('can_delete', $can_delete);
		}
	}

	public function actionSend() {
		if (Core\Validate::isNumber($this->injector['URI']->id) === true &&
				$this->injector['Db']->fetchColumn('SELECT COUNT(*) FROM ' . DB_PRE . 'newsletters WHERE id = ?', array($this->injector['URI']->id)) == 1) {
			$settings = Core\Config::getSettings('newsletter');
			$newsletter = $this->injector['Db']->fetchAssoc('SELECT title, text FROM ' . DB_PRE . 'newsletters WHERE id = ?', array($this->injector['URI']->id));

			$subject = html_entity_decode($newsletter['title'], ENT_QUOTES, 'UTF-8');
			$body = html_entity_decode($newsletter['text'] . "\n-- \n" . $settings['mailsig'], ENT_QUOTES, 'UTF-8');

			$bool = NewsletterFunctions::sendNewsletter($subject, $body, $settings['mail']);
			$bool2 = false;
			if ($bool === true) {
				$bool2 = $this->injector['Db']->update(DB_PRE . 'newsletters', array('status' => '1'), array('id' => $this->injector['URI']->id));
			}

			Core\Functions::setRedirectMessage($bool && $bool2, $this->injector['Lang']->t('newsletter', $bool === true && $bool2 !== false ? 'create_success' : 'create_save_error'), 'acp/newsletter');
		} else {
			$this->injector['URI']->redirect('errors/404');
		}
	}

	public function actionSettings() {
		if (isset($_POST['submit']) === true) {
			if (Core\Validate::email($_POST['mail']) === false)
				$errors['mail'] = $this->injector['Lang']->t('system', 'wrong_email_format');

			if (isset($errors) === true) {
				$this->injector['View']->assign('error_msg', Core\Functions::errorBox($errors));
			} elseif (Core\Validate::formToken() === false) {
				$this->injector['View']->setContent(Core\Functions::errorBox($this->injector['Lang']->t('system', 'form_already_submitted')));
			} else {
				$data = array(
					'mail' => $_POST['mail'],
					'mailsig' => Core\Functions::str_encode($_POST['mailsig'])
				);

				$bool = Core\Config::setSettings('newsletter', $data);

				$this->injector['Session']->unsetFormToken();

				Core\Functions::setRedirectMessage($bool, $this->injector['Lang']->t('system', $bool === true ? 'settings_success' : 'settings_error'), 'acp/newsletter');
			}
		}
		if (isset($_POST['submit']) === false || isset($errors) === true && is_array($errors) === true) {
			$settings = Core\Config::getSettings('newsletter');

			$this->injector['View']->assign('form', isset($_POST['submit']) ? $_POST : $settings);

			$this->injector['Session']->generateFormToken();
		}
	}

}