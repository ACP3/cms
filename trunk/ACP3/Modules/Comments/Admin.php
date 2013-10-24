<?php

namespace ACP3\Modules\Comments;

use ACP3\Core;

/**
 * Description of CommentsAdmin
 *
 * @author Tino Goratsch
 */
class Admin extends Core\ModuleController {

	public function __construct() {
		parent::__construct();
	}

	public function actionDelete() {
		if (isset($_POST['entries']) && is_array($_POST['entries']) === true)
			$entries = $_POST['entries'];
		elseif (Core\Validate::deleteEntries($this->uri->entries) === true)
			$entries = $this->uri->entries;

		if (!isset($entries)) {
			$this->view->setContent(Core\Functions::errorBox($this->lang->t('system', 'no_entries_selected')));
		} elseif (is_array($entries) === true) {
			$marked_entries = implode('|', $entries);
			$this->view->setContent(Core\Functions::confirmBox($this->lang->t('system', 'confirm_delete'), $this->uri->route('acp/comments/delete/entries_' . $marked_entries . '/action_confirmed/'), $this->uri->route('acp/comments')));
		} elseif ($this->uri->action === 'confirmed') {
			$marked_entries = explode('|', $entries);
			$bool = false;
			foreach ($marked_entries as $entry) {
				$bool = $this->db->delete(DB_PRE . 'comments', array('module_id' => $entry));
			}
			Core\Functions::setRedirectMessage($bool, $this->lang->t('system', $bool !== false ? 'delete_success' : 'delete_error'), 'acp/comments');
		} else {
			$this->uri->redirect('errors/404');
		}
	}

	public function actionDeleteComments() {
		if (isset($_POST['entries']) && is_array($_POST['entries']) === true)
			$entries = $_POST['entries'];
		elseif (Core\Validate::deleteEntries($this->uri->entries) === true)
			$entries = $this->uri->entries;

		if (!isset($entries)) {
			$this->view->setContent(Core\Functions::errorBox($this->lang->t('system', 'no_entries_selected')));
		} elseif (is_array($entries) === true) {
			$marked_entries = implode('|', $entries);
			$this->view->setContent(Core\Functions::confirmBox($this->lang->t('system', 'confirm_delete'), $this->uri->route('acp/comments/delete_comments/entries_' . $marked_entries . '/action_confirmed/'), $this->uri->route('acp/comments')));
		} elseif ($this->uri->action === 'confirmed') {
			$marked_entries = explode('|', $entries);
			$bool = false;
			foreach ($marked_entries as $entry) {
				$bool = $this->db->delete(DB_PRE . 'comments', array('id' => $entry));
			}
			Core\Functions::setRedirectMessage($bool, $this->lang->t('system', $bool !== false ? 'delete_success' : 'delete_error'), 'acp/comments');
		} else {
			$this->uri->redirect('errors/404');
		}
	}

	public function actionEdit() {
		if (Core\Validate::isNumber($this->uri->id) === true &&
				$this->db->fetchColumn('SELECT COUNT(*) FROM ' . DB_PRE . 'comments WHERE id = ?', array($this->uri->id)) == 1) {
			$comment = $this->db->fetchAssoc('SELECT c.name, c.user_id, c.message, c.module_id, m.name AS module FROM ' . DB_PRE . 'comments AS c JOIN ' . DB_PRE . 'modules AS m ON(m.id = c.module_id) WHERE c.id = ?', array($this->uri->id));

			$this->breadcrumb
					->append($this->lang->t($comment['module'], $comment['module']), $this->uri->route('acp/comments/list_comments/id_' . $comment['module_id']))
					->append($this->lang->t('comments', 'acp_edit'));

			if (isset($_POST['submit']) === true) {
				if ((empty($comment['user_id']) || Core\Validate::isNumber($comment['user_id']) === false) && empty($_POST['name']))
					$errors['name'] = $this->lang->t('system', 'name_to_short');
				if (strlen($_POST['message']) < 3)
					$errors['message'] = $this->lang->t('system', 'message_to_short');

				if (isset($errors) === true) {
					$this->view->assign('error_msg', Core\Functions::errorBox($errors));
				} elseif (Core\Validate::formToken() === false) {
					$this->view->setContent(Core\Functions::errorBox($this->lang->t('system', 'form_already_submitted')));
				} else {
					$update_values = array();
					$update_values['message'] = Core\Functions::strEncode($_POST['message']);
					if ((empty($comment['user_id']) || Core\Validate::isNumber($comment['user_id']) === false) && !empty($_POST['name'])) {
						$update_values['name'] = Core\Functions::strEncode($_POST['name']);
					}

					$bool = $this->db->update(DB_PRE . 'comments', $update_values, array('id' => $this->uri->id));

					$this->session->unsetFormToken();

					Core\Functions::setRedirectMessage($bool, $this->lang->t('system', $bool !== false ? 'edit_success' : 'edit_error'), 'acp/comments/list_comments/id_' . $comment['module_id']);
				}
			}
			if (isset($_POST['submit']) === false || isset($errors) === true && is_array($errors) === true) {
				if (Core\Modules::isActive('emoticons') === true) {
					// Emoticons im Formular anzeigen
					$this->view->assign('emoticons', \ACP3\Modules\Emoticons\Helpers::emoticonsList());
				}

				$this->view->assign('form', isset($_POST['submit']) ? $_POST : $comment);
				$this->view->assign('module_id', (int) $comment['module_id']);

				$this->session->generateFormToken();
			}
		} else {
			$this->uri->redirect('errors/404');
		}
	}

	public function actionList() {
		Core\Functions::getRedirectMessage();

		$comments = $this->db->fetchAll('SELECT c.module_id, m.name AS module, COUNT(c.module_id) AS `comments_count` FROM ' . DB_PRE . 'comments AS c JOIN ' . DB_PRE . 'modules AS m ON(m.id = c.module_id) GROUP BY c.module_id ORDER BY m.name');
		$c_comments = count($comments);

		if ($c_comments > 0) {
			$can_delete = Core\Modules::hasPermission('comments', 'acp_delete');
			$config = array(
				'element' => '#acp-table',
				'sort_col' => $can_delete === true ? 1 : 0,
				'sort_dir' => 'desc',
				'hide_col_sort' => $can_delete === true ? 0 : ''
			);
			$this->view->appendContent(Core\Functions::datatable($config));
			for ($i = 0; $i < $c_comments; ++$i) {
				$comments[$i]['name'] = $this->lang->t($comments[$i]['module'], $comments[$i]['module']);
			}
			$this->view->assign('comments', $comments);
			$this->view->assign('can_delete', $can_delete);
		}
	}

	public function actionListComments() {
		Core\Functions::getRedirectMessage();

		if (Core\Validate::isNumber($this->uri->id) &&
				$this->db->fetchColumn('SELECT COUNT(*) FROM ' . DB_PRE . 'comments WHERE module_id = ?', array($this->uri->id)) > 0) {
			$module = $this->db->fetchColumn('SELECT name FROM ' . DB_PRE . 'modules WHERE id = ?', array($this->uri->id));

			//Brotkrümelspur
			$this->breadcrumb->append($this->lang->t($module, $module));

			$comments = $this->db->fetchAll('SELECT IF(c.name != "" AND c.user_id = 0,c.name,u.nickname) AS name, c.id, c.ip, c.user_id, c.date, c.message FROM ' . DB_PRE . 'comments AS c LEFT JOIN ' . DB_PRE . 'users AS u ON u.id = c.user_id WHERE c.module_id = ? ORDER BY c.entry_id ASC, c.id ASC', array($this->uri->id));
			$c_comments = count($comments);

			if ($c_comments > 0) {
				$can_delete = Core\Modules::hasPermission('comments', 'acp_delete_comments');
				$config = array(
					'element' => '#acp-table',
					'sort_col' => $can_delete === true ? 5 : 4,
					'sort_dir' => 'asc',
					'hide_col_sort' => $can_delete === true ? 0 : ''
				);
				$this->view->appendContent(Core\Functions::datatable($config));

				$settings = Core\Config::getSettings('comments');
				// Emoticons einbinden
				$emoticons_active = false;
				if ($settings['emoticons'] == 1) {
					if (Core\Modules::isActive('emoticons') === true) {
						$emoticons_active = true;
					}
				}

				for ($i = 0; $i < $c_comments; ++$i) {
					if (!empty($comments[$i]['user_id']) && empty($comments[$i]['name'])) {
						$comments[$i]['name'] = $this->lang->t('users', 'deleted_user');
					}
					$comments[$i]['date_formatted'] = $this->date->formatTimeRange($comments[$i]['date']);
					$comments[$i]['message'] = Core\Functions::nl2p($comments[$i]['message']);
					if ($emoticons_active === true) {
						$comments[$i]['message'] = \ACP3\Modules\Emoticons\Helpers::emoticonsReplace($comments[$i]['message']);
					}
				}
				$this->view->assign('comments', $comments);
				$this->view->assign('can_delete', $can_delete);
			}
		}
	}

	public function actionSettings() {
		$emoticons_active = Core\Modules::isActive('emoticons');

		if (isset($_POST['submit']) === true) {
			if (empty($_POST['dateformat']) || ($_POST['dateformat'] !== 'long' && $_POST['dateformat'] !== 'short'))
				$errors['dateformat'] = $this->lang->t('system', 'select_date_format');
			if ($emoticons_active === true && (!isset($_POST['emoticons']) || ($_POST['emoticons'] != 0 && $_POST['emoticons'] != 1)))
				$errors[] = $this->lang->t('comments', 'select_emoticons');

			if (isset($errors) === true) {
				$this->view->assign('error_msg', Core\Functions::errorBox($errors));
			} elseif (Core\Validate::formToken() === false) {
				$this->view->setContent(Core\Functions::errorBox($this->lang->t('system', 'form_already_submitted')));
			} else {
				$data = array(
					'dateformat' => Core\Functions::strEncode($_POST['dateformat']),
					'emoticons' => $_POST['emoticons'],
				);
				$bool = Core\Config::setSettings('comments', $data);

				$this->session->unsetFormToken();

				Core\Functions::setRedirectMessage($bool, $this->lang->t('system', $bool === true ? 'settings_success' : 'settings_error'), 'acp/comments');
			}
		}
		if (isset($_POST['submit']) === false || isset($errors) === true && is_array($errors) === true) {
			$settings = Core\Config::getSettings('comments');

			$this->view->assign('dateformat', $this->date->dateformatDropdown($settings['dateformat']));

			// Emoticons erlauben
			if ($emoticons_active === true) {
				$lang_allow_emoticons = array($this->lang->t('system', 'yes'), $this->lang->t('system', 'no'));
				$this->view->assign('allow_emoticons', Core\Functions::selectGenerator('emoticons', array(1, 0), $lang_allow_emoticons, $settings['emoticons'], 'checked'));
			}

			$this->session->generateFormToken();
		}
	}

}