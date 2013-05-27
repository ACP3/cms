<?php

namespace ACP3\Modules\Newsletter;

use ACP3\Core;

/**
 * Description of NewsletterAdmin
 *
 * @author Tino Goratsch
 */
class NewsletterAdmin extends Core\ModuleController {

	public function actionActivate() {
		$bool = false;
		if (Core\Validate::isNumber(Core\Registry::get('URI')->id) === true) {
			$bool = Core\Registry::get('Db')->update(DB_PRE . 'newsletter_accounts', array('hash' => ''), array('id' => Core\Registry::get('URI')->id));
		}

		Core\Functions::setRedirectMessage($bool, Core\Registry::get('Lang')->t('newsletter', $bool !== false ? 'activate_success' : 'activate_error'), 'acp/newsletter');
	}

	public function actionCreate() {
		if (isset($_POST['submit']) === true) {
			if (strlen($_POST['title']) < 3)
				$errors['title'] = Core\Registry::get('Lang')->t('newsletter', 'subject_to_short');
			if (strlen($_POST['text']) < 3)
				$errors['text'] = Core\Registry::get('Lang')->t('newsletter', 'text_to_short');

			if (isset($errors) === true) {
				Core\Registry::get('View')->assign('error_msg', Core\Functions::errorBox($errors));
			} elseif (Core\Validate::formToken() === false) {
				Core\Registry::get('View')->setContent(Core\Functions::errorBox(Core\Registry::get('Lang')->t('system', 'form_already_submitted')));
			} else {
				$settings = Core\Config::getSettings('newsletter');

				// Newsletter archivieren
				$insert_values = array(
					'id' => '',
					'date' => Core\Registry::get('Date')->getCurrentDateTime(),
					'title' => Core\Functions::strEncode($_POST['title']),
					'text' => Core\Functions::strEncode($_POST['text'], true),
					'status' => $_POST['test'] == 1 ? '0' : (int) $_POST['action'],
					'user_id' => Core\Registry::get('Auth')->getUserId(),
				);
				$bool = Core\Registry::get('Db')->insert(DB_PRE . 'newsletters', $insert_values);

				if ($_POST['action'] == 1 && $bool !== false) {
					$subject = Core\Functions::strEncode($_POST['title'], true);
					$body = Core\Functions::strEncode($_POST['text'], true) . "\n-- \n" . html_entity_decode($settings['mailsig'], ENT_QUOTES, 'UTF-8');

					// Testnewsletter
					if ($_POST['test'] == 1) {
						$bool2 = Core\Functions::generateEmail('', $settings['mail'], $settings['mail'], $subject, $body);
						// An alle versenden
					} else {
						$bool2 = NewsletterFunctions::sendNewsletter($subject, $body, $settings['mail']);
					}
				}

				Core\Registry::get('Session')->unsetFormToken();

				if ($_POST['action'] == 0 && $bool !== false) {
					Core\Functions::setRedirectMessage(true, Core\Registry::get('Lang')->t('newsletter', 'save_success'), 'acp/newsletter');
				} elseif ($_POST['action'] == 1 && $bool !== false && $bool2 === true) {
					Core\Functions::setRedirectMessage($bool && $bool2, Core\Registry::get('Lang')->t('newsletter', 'create_success'), 'acp/newsletter');
				} else {
					Core\Functions::setRedirectMessage(false, Core\Registry::get('Lang')->t('newsletter', 'create_save_error'), 'acp/newsletter');
				}
			}
		}
		if (isset($_POST['submit']) === false || isset($errors) === true && is_array($errors) === true) {
			Core\Registry::get('View')->assign('form', isset($_POST['submit']) ? $_POST : array('title' => '', 'text' => ''));

			$lang_test = array(Core\Registry::get('Lang')->t('system', 'yes'), Core\Registry::get('Lang')->t('system', 'no'));
			Core\Registry::get('View')->assign('test', Core\Functions::selectGenerator('test', array(1, 0), $lang_test, 0, 'checked'));

			$lang_action = array(Core\Registry::get('Lang')->t('newsletter', 'send_and_save'), Core\Registry::get('Lang')->t('newsletter', 'only_save'));
			Core\Registry::get('View')->assign('action', Core\Functions::selectGenerator('action', array(1, 0), $lang_action, 1, 'checked'));

			Core\Registry::get('Session')->generateFormToken();
		}
	}

	public function actionDelete() {
		if (isset($_POST['entries']) && is_array($_POST['entries']) === true)
			$entries = $_POST['entries'];
		elseif (Core\Validate::deleteEntries(Core\Registry::get('URI')->entries) === true)
			$entries = Core\Registry::get('URI')->entries;

		if (!isset($entries)) {
			Core\Registry::get('View')->setContent(Core\Functions::errorBox(Core\Registry::get('Lang')->t('system', 'no_entries_selected')));
		} elseif (is_array($entries) === true) {
			$marked_entries = implode('|', $entries);
			Core\Registry::get('View')->setContent(Core\Functions::confirmBox(Core\Registry::get('Lang')->t('system', 'confirm_delete'), Core\Registry::get('URI')->route('acp/newsletter/delete/entries_' . $marked_entries . '/action_confirmed/'), Core\Registry::get('URI')->route('acp/newsletter')));
		} elseif (Core\Registry::get('URI')->action === 'confirmed') {
			$marked_entries = explode('|', $entries);
			$bool = false;
			foreach ($marked_entries as $entry) {
				$bool = Core\Registry::get('Db')->delete(DB_PRE . 'newsletters', array('id' => $entry));
			}
			Core\Functions::setRedirectMessage($bool, Core\Registry::get('Lang')->t('system', $bool !== false ? 'delete_success' : 'delete_error'), 'acp/newsletter');
		} else {
			Core\Registry::get('URI')->redirect('errors/404');
		}
	}

	public function actionDeleteAccount() {
		if (isset($_POST['entries']) && is_array($_POST['entries']) === true)
			$entries = $_POST['entries'];
		elseif (Core\Validate::deleteEntries(Core\Registry::get('URI')->entries) === true)
			$entries = Core\Registry::get('URI')->entries;

		if (!isset($entries)) {
			Core\Registry::get('View')->setContent(Core\Functions::errorBox(Core\Registry::get('Lang')->t('system', 'no_entries_selected')));
		} elseif (is_array($entries) === true) {
			$marked_entries = implode('|', $entries);
			Core\Registry::get('View')->setContent(Core\Functions::confirmBox(Core\Registry::get('Lang')->t('system', 'confirm_delete'), Core\Registry::get('URI')->route('acp/newsletter/delete_account/entries_' . $marked_entries . '/action_confirmed/'), Core\Registry::get('URI')->route('acp/newsletter/list_accounts')));
		} elseif (Core\Registry::get('URI')->action === 'confirmed') {
			$marked_entries = explode('|', $entries);
			$bool = false;
			foreach ($marked_entries as $entry) {
				$bool = Core\Registry::get('Db')->delete(DB_PRE . 'newsletter_accounts', array('id' => $entry));
			}
			Core\Functions::setRedirectMessage($bool, Core\Registry::get('Lang')->t('system', $bool !== false ? 'delete_success' : 'delete_error'), 'acp/newsletter/list_accounts');
		} else {
			Core\Registry::get('URI')->redirect('errors/404');
		}
	}

	public function actionEdit() {
		if (Core\Validate::isNumber(Core\Registry::get('URI')->id) === true &&
				Core\Registry::get('Db')->fetchColumn('SELECT COUNT(*) FROM ' . DB_PRE . 'newsletters WHERE id = ?', array(Core\Registry::get('URI')->id)) == 1) {
			// Brotkrümelspur
			Core\Registry::get('Breadcrumb')
					->append(Core\Registry::get('Lang')->t('newsletter', 'newsletter'), Core\Registry::get('URI')->route('acp/newsletter'))
					->append(Core\Registry::get('Lang')->t('newsletter', 'acp_edit'));

			if (isset($_POST['submit']) === true) {
				if (strlen($_POST['title']) < 3)
					$errors['title'] = Core\Registry::get('Lang')->t('newsletter', 'subject_to_short');
				if (strlen($_POST['text']) < 3)
					$errors['text'] = Core\Registry::get('Lang')->t('newsletter', 'text_to_short');

				if (isset($errors) === true) {
					Core\Registry::get('View')->assign('error_msg', Core\Functions::errorBox($errors));
				} elseif (Core\Validate::formToken() === false) {
					Core\Registry::get('View')->setContent(Core\Functions::errorBox(Core\Registry::get('Lang')->t('system', 'form_already_submitted')));
				} else {
					$settings = Core\Config::getSettings('newsletter');

					// Newsletter archivieren
					$update_values = array(
						'date' => Core\Registry::get('Date')->getCurrentDateTime(),
						'title' => Core\Functions::strEncode($_POST['title']),
						'text' => Core\Functions::strEncode($_POST['text'], true),
						'status' => $_POST['test'] == 1 ? '0' : (int) $_POST['action'],
						'user_id' => Core\Registry::get('Auth')->getUserId(),
					);
					$bool = Core\Registry::get('Db')->update(DB_PRE . 'newsletters', $update_values, array('id' => Core\Registry::get('URI')->id));

					if ($_POST['action'] == 1 && $bool !== false) {
						$subject = Core\Functions::strEncode($_POST['title'], true);
						$body = Core\Functions::strEncode($_POST['text'], true) . "\n" . html_entity_decode($settings['mailsig'], ENT_QUOTES, 'UTF-8');

						// Testnewsletter
						if ($_POST['test'] == 1) {
							$bool2 = Core\Functions::generateEmail('', $settings['mail'], $settings['mail'], $subject, $body);
						// An alle versenden
						} else {
							$bool2 = NewsletterFunctions::sendNewsletter($subject, $body, $settings['mail']);
						}
					}

					Core\Registry::get('Session')->unsetFormToken();

					if ($_POST['action'] == 0 && $bool !== false) {
						Core\Functions::setRedirectMessage(true, Core\Registry::get('Lang')->t('newsletter', 'save_success'), 'acp/newsletter');
					} elseif ($_POST['action'] == 1 && $bool !== false && $bool2 === true) {
						Core\Functions::setRedirectMessage($bool && $bool2, Core\Registry::get('Lang')->t('newsletter', 'create_success'), 'acp/newsletter');
					} else {
						Core\Functions::setRedirectMessage(false, Core\Registry::get('Lang')->t('newsletter', 'create_save_error'), 'acp/newsletter');
					}
				}
			}
			if (isset($_POST['submit']) === false || isset($errors) === true && is_array($errors) === true) {
				$newsletter = Core\Registry::get('Db')->fetchAssoc('SELECT title, text FROM ' . DB_PRE . 'newsletters WHERE id = ?', array(Core\Registry::get('URI')->id));

				Core\Registry::get('View')->assign('form', isset($_POST['submit']) ? $_POST : $newsletter);

				$lang_test = array(Core\Registry::get('Lang')->t('system', 'yes'), Core\Registry::get('Lang')->t('system', 'no'));
				Core\Registry::get('View')->assign('test', Core\Functions::selectGenerator('test', array(1, 0), $lang_test, 0, 'checked'));

				$lang_action = array(Core\Registry::get('Lang')->t('newsletter', 'send_and_save'), Core\Registry::get('Lang')->t('newsletter', 'only_save'));
				Core\Registry::get('View')->assign('action', Core\Functions::selectGenerator('action', array(1, 0), $lang_action, 1, 'checked'));

				Core\Registry::get('Session')->generateFormToken();
			}
		} else {
			Core\Registry::get('URI')->redirect('errors/404');
		}
	}

	public function actionList() {
		Core\Functions::getRedirectMessage();

		$newsletter = Core\Registry::get('Db')->fetchAll('SELECT id, date, title, status FROM ' . DB_PRE . 'newsletters ORDER BY date DESC');
		$c_newsletter = count($newsletter);

		if ($c_newsletter > 0) {
			$can_delete = Core\Modules::hasPermission('newsletter', 'acp_delete');
			$config = array(
				'element' => '#acp-table',
				'sort_col' => $can_delete === true ? 1 : 0,
				'sort_dir' => 'desc',
				'hide_col_sort' => $can_delete === true ? 0 : ''
			);
			Core\Registry::get('View')->appendContent(Core\Functions::datatable($config));

			$search = array('0', '1');
			$replace = array(Core\Registry::get('Lang')->t('newsletter', 'not_yet_sent'), Core\Registry::get('Lang')->t('newsletter', 'already_sent'));
			for ($i = 0; $i < $c_newsletter; ++$i) {
				$newsletter[$i]['date_formatted'] = Core\Registry::get('Date')->formatTimeRange($newsletter[$i]['date']);
				$newsletter[$i]['status'] = str_replace($search, $replace, $newsletter[$i]['status']);
			}
			Core\Registry::get('View')->assign('newsletter', $newsletter);
			Core\Registry::get('View')->assign('can_delete', $can_delete);
			Core\Registry::get('View')->assign('can_send', Core\Modules::hasPermission('newsletter', 'acp_send'));
		}
	}

	public function actionListAccounts() {
		Core\Functions::getRedirectMessage();

		$accounts = Core\Registry::get('Db')->fetchAll('SELECT id, mail, hash FROM ' . DB_PRE . 'newsletter_accounts ORDER BY id DESC');
		$c_accounts = count($accounts);

		if ($c_accounts > 0) {
			$can_delete = Core\Modules::hasPermission('newsletter', 'acp_delete_account');
			$config = array(
				'element' => '#acp-table',
				'sort_col' => $can_delete === true ? 3 : 2,
				'sort_dir' => 'desc',
				'hide_col_sort' => $can_delete === true ? 0 : ''
			);
			Core\Registry::get('View')->appendContent(Core\Functions::datatable($config));

			Core\Registry::get('View')->assign('accounts', $accounts);
			Core\Registry::get('View')->assign('can_delete', $can_delete);
		}
	}

	public function actionSend() {
		if (Core\Validate::isNumber(Core\Registry::get('URI')->id) === true &&
				Core\Registry::get('Db')->fetchColumn('SELECT COUNT(*) FROM ' . DB_PRE . 'newsletters WHERE id = ?', array(Core\Registry::get('URI')->id)) == 1) {
			$settings = Core\Config::getSettings('newsletter');
			$newsletter = Core\Registry::get('Db')->fetchAssoc('SELECT title, text FROM ' . DB_PRE . 'newsletters WHERE id = ?', array(Core\Registry::get('URI')->id));

			$subject = html_entity_decode($newsletter['title'], ENT_QUOTES, 'UTF-8');
			$body = html_entity_decode($newsletter['text'] . "\n-- \n" . $settings['mailsig'], ENT_QUOTES, 'UTF-8');

			$bool = NewsletterFunctions::sendNewsletter($subject, $body, $settings['mail']);
			$bool2 = false;
			if ($bool === true) {
				$bool2 = Core\Registry::get('Db')->update(DB_PRE . 'newsletters', array('status' => '1'), array('id' => Core\Registry::get('URI')->id));
			}

			Core\Functions::setRedirectMessage($bool && $bool2, Core\Registry::get('Lang')->t('newsletter', $bool === true && $bool2 !== false ? 'create_success' : 'create_save_error'), 'acp/newsletter');
		} else {
			Core\Registry::get('URI')->redirect('errors/404');
		}
	}

	public function actionSettings() {
		if (isset($_POST['submit']) === true) {
			if (Core\Validate::email($_POST['mail']) === false)
				$errors['mail'] = Core\Registry::get('Lang')->t('system', 'wrong_email_format');

			if (isset($errors) === true) {
				Core\Registry::get('View')->assign('error_msg', Core\Functions::errorBox($errors));
			} elseif (Core\Validate::formToken() === false) {
				Core\Registry::get('View')->setContent(Core\Functions::errorBox(Core\Registry::get('Lang')->t('system', 'form_already_submitted')));
			} else {
				$data = array(
					'mail' => $_POST['mail'],
					'mailsig' => Core\Functions::strEncode($_POST['mailsig'])
				);

				$bool = Core\Config::setSettings('newsletter', $data);

				Core\Registry::get('Session')->unsetFormToken();

				Core\Functions::setRedirectMessage($bool, Core\Registry::get('Lang')->t('system', $bool === true ? 'settings_success' : 'settings_error'), 'acp/newsletter');
			}
		}
		if (isset($_POST['submit']) === false || isset($errors) === true && is_array($errors) === true) {
			$settings = Core\Config::getSettings('newsletter');

			Core\Registry::get('View')->assign('form', isset($_POST['submit']) ? $_POST : $settings);

			Core\Registry::get('Session')->generateFormToken();
		}
	}

}