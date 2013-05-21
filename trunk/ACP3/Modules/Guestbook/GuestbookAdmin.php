<?php

namespace ACP3\Modules\Guestbook;

use ACP3\Core;

/**
 * Description of GuestbookAdmin
 *
 * @author Tino
 */
class GuestbookAdmin extends Core\ModuleController {

	public function actionDelete() {
		if (isset($_POST['entries']) && is_array($_POST['entries']) === true)
			$entries = $_POST['entries'];
		elseif (Core\Validate::deleteEntries(Core\Registry::get('URI')->entries) === true)
			$entries = Core\Registry::get('URI')->entries;

		if (!isset($entries)) {
			Core\Registry::get('View')->setContent(Core\Functions::errorBox(Core\Registry::get('Lang')->t('system', 'no_entries_selected')));
		} elseif (is_array($entries) === true) {
			$marked_entries = implode('|', $entries);
			Core\Registry::get('View')->setContent(Core\Functions::confirmBox(Core\Registry::get('Lang')->t('system', 'confirm_delete'), Core\Registry::get('URI')->route('acp/guestbook/delete/entries_' . $marked_entries . '/action_confirmed/'), Core\Registry::get('URI')->route('acp/guestbook')));
		} elseif (Core\Registry::get('URI')->action === 'confirmed') {
			$marked_entries = explode('|', $entries);
			$bool = false;
			foreach ($marked_entries as $entry) {
				$bool = Core\Registry::get('Db')->delete(DB_PRE . 'guestbook', array('id' => $entry));
			}
			Core\Functions::setRedirectMessage($bool, Core\Registry::get('Lang')->t('system', $bool !== false ? 'delete_success' : 'delete_error'), 'acp/guestbook');
		} else {
			Core\Registry::get('URI')->redirect('errors/404');
		}
	}

	public function actionEdit() {
		if (Core\Validate::isNumber(Core\Registry::get('URI')->id) === true &&
				Core\Registry::get('Db')->fetchColumn('SELECT COUNT(*) FROM ' . DB_PRE . 'guestbook WHERE id = ?', array(Core\Registry::get('URI')->id)) == 1) {
			$settings = Core\Config::getSettings('guestbook');

			if (isset($_POST['submit']) === true) {
				if (empty($_POST['name']))
					$errors['name'] = Core\Registry::get('Lang')->t('system', 'name_to_short');
				if (strlen($_POST['message']) < 3)
					$errors['message'] = Core\Registry::get('Lang')->t('system', 'message_to_short');
				if ($settings['notify'] == 2 && (!isset($_POST['active']) || ($_POST['active'] != 0 && $_POST['active'] != 1)))
					$errors['notify'] = Core\Registry::get('Lang')->t('guestbook', 'select_activate');

				if (isset($errors) === true) {
					Core\Registry::get('View')->assign('error_msg', Core\Functions::errorBox($errors));
				} elseif (Core\Validate::formToken() === false) {
					Core\Registry::get('View')->setContent(Core\Functions::errorBox(Core\Registry::get('Lang')->t('system', 'form_already_submitted')));
				} else {
					$update_values = array(
						'name' => Core\Functions::str_encode($_POST['name']),
						'message' => Core\Functions::str_encode($_POST['message']),
						'active' => $settings['notify'] == 2 ? $_POST['active'] : 1,
					);

					$bool = Core\Registry::get('Db')->update(DB_PRE . 'guestbook', $update_values, array('id' => Core\Registry::get('URI')->id));

					Core\Registry::get('Session')->unsetFormToken();

					Core\Functions::setRedirectMessage($bool, Core\Registry::get('Lang')->t('system', $bool !== false ? 'edit_success' : 'edit_error'), 'acp/guestbook');
				}
			}
			if (isset($_POST['submit']) === false || isset($errors) === true && is_array($errors) === true) {
				$guestbook = Core\Registry::get('Db')->fetchAssoc('SELECT name, message, active FROM ' . DB_PRE . 'guestbook WHERE id = ?', array(Core\Registry::get('URI')->id));

				if ($settings['emoticons'] == 1 && Core\Modules::isActive('emoticons') === true) {
					//Emoticons im Formular anzeigen
					Core\Registry::get('View')->assign('emoticons', \ACP3\Modules\Emoticons\EmoticonsFunctions::emoticonsList());
				}

				if ($settings['notify'] == 2) {
					$activate = array();
					$activate[0]['value'] = '1';
					$activate[0]['checked'] = Core\Functions::selectEntry('active', '1', $guestbook['active'], 'checked');
					$activate[0]['lang'] = Core\Registry::get('Lang')->t('system', 'yes');
					$activate[1]['value'] = '0';
					$activate[1]['checked'] = Core\Functions::selectEntry('active', '0', $guestbook['active'], 'checked');
					$activate[1]['lang'] = Core\Registry::get('Lang')->t('system', 'no');
					Core\Registry::get('View')->assign('activate', $activate);
				}

				Core\Registry::get('View')->assign('form', isset($_POST['submit']) ? $_POST : $guestbook);

				Core\Registry::get('Session')->generateFormToken();
			}
		} else {
			Core\Registry::get('URI')->redirect('errors/404');
		}
	}

	public function actionList() {
		Core\Functions::getRedirectMessage();

		$guestbook = Core\Registry::get('Db')->fetchAll('SELECT id, ip, date, name, message FROM ' . DB_PRE . 'guestbook ORDER BY date DESC');
		$c_guestbook = count($guestbook);

		if ($c_guestbook > 0) {
			$can_delete = Core\Modules::hasPermission('guestbook', 'acp_delete');
			$config = array(
				'element' => '#acp-table',
				'sort_col' => $can_delete === true ? 1 : 0,
				'sort_dir' => 'desc',
				'hide_col_sort' => $can_delete === true ? 0 : ''
			);
			Core\Registry::get('View')->appendContent(Core\Functions::datatable($config));

			$settings = Core\Config::getSettings('guestbook');
			// Emoticons einbinden
			$emoticons_active = false;
			if ($settings['emoticons'] == 1) {
				if (Core\Modules::isActive('emoticons') === true) {
					$emoticons_active = true;
				}
			}

			for ($i = 0; $i < $c_guestbook; ++$i) {
				$guestbook[$i]['date_formatted'] = Core\Registry::get('Date')->formatTimeRange($guestbook[$i]['date']);
				$guestbook[$i]['message'] = Core\Functions::nl2p($guestbook[$i]['message']);
				if ($emoticons_active === true) {
					$guestbook[$i]['message'] = \ACP3\Modules\Emoticons\EmoticonsFunctions::emoticonsReplace($guestbook[$i]['message']);
				}
			}
			Core\Registry::get('View')->assign('guestbook', $guestbook);
			Core\Registry::get('View')->assign('can_delete', $can_delete);
		}
	}

	public function actionSettings() {
		$emoticons_active = Core\Modules::isActive('emoticons');
		$newsletter_active = Core\Modules::isActive('newsletter');

		if (isset($_POST['submit']) === true) {
			if (empty($_POST['dateformat']) || ($_POST['dateformat'] !== 'long' && $_POST['dateformat'] !== 'short'))
				$errors['dateformat'] = Core\Registry::get('Lang')->t('system', 'select_date_format');
			if (!isset($_POST['notify']) || ($_POST['notify'] != 0 && $_POST['notify'] != 1 && $_POST['notify'] != 2))
				$errors['notify'] = Core\Registry::get('Lang')->t('guestbook', 'select_notification_type');
			if ($_POST['notify'] != 0 && Core\Validate::email($_POST['notify_email']) === false)
				$errors['notify-email'] = Core\Registry::get('Lang')->t('system', 'wrong_email_format');
			if (!isset($_POST['overlay']) || $_POST['overlay'] != 1 && $_POST['overlay'] != 0)
				$errors[] = Core\Registry::get('Lang')->t('guestbook', 'select_use_overlay');
			if ($emoticons_active === true && (!isset($_POST['emoticons']) || ($_POST['emoticons'] != 0 && $_POST['emoticons'] != 1)))
				$errors[] = Core\Registry::get('Lang')->t('guestbook', 'select_emoticons');
			if ($newsletter_active === true && (!isset($_POST['newsletter_integration']) || ($_POST['newsletter_integration'] != 0 && $_POST['newsletter_integration'] != 1)))
				$errors[] = Core\Registry::get('Lang')->t('guestbook', 'select_newsletter_integration');

			if (isset($errors) === true) {
				Core\Registry::get('View')->assign('error_msg', Core\Functions::errorBox($errors));
			} elseif (Core\Validate::formToken() === false) {
				Core\Registry::get('View')->setContent(Core\Functions::errorBox(Core\Registry::get('Lang')->t('system', 'form_already_submitted')));
			} else {
				$data = array(
					'dateformat' => Core\Functions::str_encode($_POST['dateformat']),
					'notify' => $_POST['notify'],
					'notify_email' => $_POST['notify_email'],
					'overlay' => $_POST['overlay'],
					'emoticons' => $_POST['emoticons'],
					'newsletter_integration' => $_POST['newsletter_integration'],
				);
				$bool = Core\Config::setSettings('guestbook', $data);

				Core\Registry::get('Session')->unsetFormToken();

				Core\Functions::setRedirectMessage($bool, Core\Registry::get('Lang')->t('system', $bool === true ? 'settings_success' : 'settings_error'), 'acp/guestbook');
			}
		}
		if (isset($_POST['submit']) === false || isset($errors) === true && is_array($errors) === true) {
			$settings = Core\Config::getSettings('guestbook');

			Core\Registry::get('View')->assign('dateformat', Core\Registry::get('Date')->dateformatDropdown($settings['dateformat']));

			$lang_notify = array(
				Core\Registry::get('Lang')->t('guestbook', 'no_notification'),
				Core\Registry::get('Lang')->t('guestbook', 'notify_on_new_entry'),
				Core\Registry::get('Lang')->t('guestbook', 'notify_and_enable')
			);
			Core\Registry::get('View')->assign('notify', Core\Functions::selectGenerator('notify', array(0, 1, 2), $lang_notify, $settings['notify']));

			$lang_overlay = array(Core\Registry::get('Lang')->t('system', 'yes'), Core\Registry::get('Lang')->t('system', 'no'));
			Core\Registry::get('View')->assign('overlay', Core\Functions::selectGenerator('overlay', array(1, 0), $lang_overlay, $settings['overlay'], 'checked'));

			// Emoticons erlauben
			if ($emoticons_active === true) {
				$lang_allow_emoticons = array(Core\Registry::get('Lang')->t('system', 'yes'), Core\Registry::get('Lang')->t('system', 'no'));
				Core\Registry::get('View')->assign('allow_emoticons', Core\Functions::selectGenerator('emoticons', array(1, 0), $lang_allow_emoticons, $settings['emoticons'], 'checked'));
			}

			// In Newsletter integrieren
			if ($newsletter_active === true) {
				$lang_newsletter_integration = array(Core\Registry::get('Lang')->t('system', 'yes'), Core\Registry::get('Lang')->t('system', 'no'));
				Core\Registry::get('View')->assign('newsletter_integration', Core\Functions::selectGenerator('newsletter_integration', array(1, 0), $lang_newsletter_integration, $settings['newsletter_integration'], 'checked'));
			}

			Core\Registry::get('View')->assign('form', isset($_POST['submit']) ? $_POST : array('notify_email' => $settings['notify_email']));

			Core\Registry::get('Session')->generateFormToken();
		}
	}

}