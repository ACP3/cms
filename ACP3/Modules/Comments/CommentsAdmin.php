<?php

namespace ACP3\Modules\Comments;

use ACP3\Core;

/**
 * Description of CommentsAdmin
 *
 * @author Tino
 */
class CommentsAdmin extends Core\ModuleController {

	public function __construct($injector) {
		parent::__construct($injector);
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
			$this->injector['View']->setContent(confirmBox($this->injector['Lang']->t('system', 'confirm_delete'), $this->injector['URI']->route('acp/comments/delete/entries_' . $marked_entries . '/action_confirmed/'), $this->injector['URI']->route('acp/comments')));
		} elseif ($this->injector['URI']->action === 'confirmed') {
			$marked_entries = explode('|', $entries);
			$bool = false;
			foreach ($marked_entries as $entry) {
				$bool = $this->injector['Db']->delete(DB_PRE . 'comments', array('module_id' => $entry));
			}
			Core\Functions::setRedirectMessage($bool, $this->injector['Lang']->t('system', $bool !== false ? 'delete_success' : 'delete_error'), 'acp/comments');
		} else {
			$this->injector['URI']->redirect('errors/404');
		}
	}

	public function actionDelete_comments() {
		if (isset($_POST['entries']) && is_array($_POST['entries']) === true)
			$entries = $_POST['entries'];
		elseif (Core\Validate::deleteEntries($this->injector['URI']->entries) === true)
			$entries = $this->injector['URI']->entries;

		if (!isset($entries)) {
			$this->injector['View']->setContent(Core\Functions::errorBox($this->injector['Lang']->t('system', 'no_entries_selected')));
		} elseif (is_array($entries) === true) {
			$marked_entries = implode('|', $entries);
			$this->injector['View']->setContent(confirmBox($this->injector['Lang']->t('system', 'confirm_delete'), $this->injector['URI']->route('acp/comments/delete_comments/entries_' . $marked_entries . '/action_confirmed/'), $this->injector['URI']->route('acp/comments')));
		} elseif ($this->injector['URI']->action === 'confirmed') {
			$marked_entries = explode('|', $entries);
			$bool = false;
			foreach ($marked_entries as $entry) {
				$bool = $this->injector['Db']->delete(DB_PRE . 'comments', array('id' => $entry));
			}
			Core\Functions::setRedirectMessage($bool, $this->injector['Lang']->t('system', $bool !== false ? 'delete_success' : 'delete_error'), 'acp/comments');
		} else {
			$this->injector['URI']->redirect('errors/404');
		}
	}

	public function actionEdit() {
		if (Core\Validate::isNumber($this->injector['URI']->id) === true &&
				$this->injector['Db']->fetchColumn('SELECT COUNT(*) FROM ' . DB_PRE . 'comments WHERE id = ?', array($this->injector['URI']->id)) == 1) {
			$comment = $this->injector['Db']->fetchAssoc('SELECT c.name, c.user_id, c.message, c.module_id, m.name AS module FROM ' . DB_PRE . 'comments AS c JOIN ' . DB_PRE . 'modules AS m ON(m.id = c.module_id) WHERE c.id = ?', array($this->injector['URI']->id));

			$this->injector['Breadcrumb']
					->append($this->injector['Lang']->t($comment['module'], $comment['module']), $this->injector['URI']->route('acp/comments/list_comments/id_' . $comment['module_id']))
					->append($this->injector['Lang']->t('comments', 'acp_edit'));

			if (isset($_POST['submit']) === true) {
				if ((empty($comment['user_id']) || Core\Validate::isNumber($comment['user_id']) === false) && empty($_POST['name']))
					$errors['name'] = $this->injector['Lang']->t('system', 'name_to_short');
				if (strlen($_POST['message']) < 3)
					$errors['message'] = $this->injector['Lang']->t('system', 'message_to_short');

				if (isset($errors) === true) {
					$this->injector['View']->assign('error_msg', Core\Functions::errorBox($errors));
				} elseif (Core\Validate::formToken() === false) {
					$this->injector['View']->setContent(Core\Functions::errorBox($this->injector['Lang']->t('system', 'form_already_submitted')));
				} else {
					$update_values = array();
					$update_values['message'] = Core\Functions::str_encode($_POST['message']);
					if ((empty($comment['user_id']) || Core\Validate::isNumber($comment['user_id']) === false) && !empty($_POST['name'])) {
						$update_values['name'] = Core\Functions::str_encode($_POST['name']);
					}

					$bool = $this->injector['Db']->update(DB_PRE . 'comments', $update_values, array('id' => $this->injector['URI']->id));

					$this->injector['Session']->unsetFormToken();

					Core\Functions::setRedirectMessage($bool, $this->injector['Lang']->t('system', $bool !== false ? 'edit_success' : 'edit_error'), 'acp/comments/list_comments/id_' . $comment['module_id']);
				}
			}
			if (isset($_POST['submit']) === false || isset($errors) === true && is_array($errors) === true) {
				if (Core\Modules::isActive('emoticons') === true) {
					// Emoticons im Formular anzeigen
					$this->injector['View']->assign('emoticons', \ACP3\Modules\Emoticons\EmoticonsFunctions::emoticonsList());
				}

				$this->injector['View']->assign('form', isset($_POST['submit']) ? $_POST : $comment);
				$this->injector['View']->assign('module_id', (int) $comment['module_id']);

				$this->injector['Session']->generateFormToken();
			}
		} else {
			$this->injector['URI']->redirect('errors/404');
		}
	}

	public function actionList() {
		Core\Functions::getRedirectMessage();

		$comments = $this->injector['Db']->fetchAll('SELECT c.module_id, m.name AS module, COUNT(c.module_id) AS `comments_count` FROM ' . DB_PRE . 'comments AS c JOIN ' . DB_PRE . 'modules AS m ON(m.id = c.module_id) GROUP BY c.module_id ORDER BY m.name');
		$c_comments = count($comments);

		if ($c_comments > 0) {
			$can_delete = Core\Modules::check('comments', 'acp_delete');
			$config = array(
				'element' => '#acp-table',
				'sort_col' => $can_delete === true ? 1 : 0,
				'sort_dir' => 'desc',
				'hide_col_sort' => $can_delete === true ? 0 : ''
			);
			$this->injector['View']->appendContent(Core\Functions::datatable($config));
			for ($i = 0; $i < $c_comments; ++$i) {
				$comments[$i]['name'] = $this->injector['Lang']->t($comments[$i]['module'], $comments[$i]['module']);
			}
			$this->injector['View']->assign('comments', $comments);
			$this->injector['View']->assign('can_delete', $can_delete);
		}
	}

	public function actionList_comments() {
		Core\Functions::getRedirectMessage();

		if (Core\Validate::isNumber($this->injector['URI']->id) &&
				$this->injector['Db']->fetchColumn('SELECT COUNT(*) FROM ' . DB_PRE . 'comments WHERE module_id = ?', array($this->injector['URI']->id)) > 0) {
			$module = $this->injector['Db']->fetchColumn('SELECT name FROM ' . DB_PRE . 'modules WHERE id = ?', array($this->injector['URI']->id));

			//Brotkrümelspur
			$this->injector['Breadcrumb']->append($this->injector['Lang']->t($module, $module));

			$comments = $this->injector['Db']->fetchAll('SELECT IF(c.name != "" AND c.user_id = 0,c.name,u.nickname) AS name, c.id, c.ip, c.user_id, c.date, c.message FROM ' . DB_PRE . 'comments AS c LEFT JOIN ' . DB_PRE . 'users AS u ON u.id = c.user_id WHERE c.module_id = ? ORDER BY c.entry_id ASC, c.id ASC', array($this->injector['URI']->id));
			$c_comments = count($comments);

			if ($c_comments > 0) {
				$can_delete = Core\Modules::check('comments', 'acp_delete_comments');
				$config = array(
					'element' => '#acp-table',
					'sort_col' => $can_delete === true ? 5 : 4,
					'sort_dir' => 'asc',
					'hide_col_sort' => $can_delete === true ? 0 : ''
				);
				$this->injector['View']->appendContent(Core\Functions::datatable($config));

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
						$comments[$i]['name'] = $this->injector['Lang']->t('users', 'deleted_user');
					}
					$comments[$i]['date_formatted'] = $this->injector['Date']->formatTimeRange($comments[$i]['date']);
					$comments[$i]['message'] = Core\Functions::nl2p($comments[$i]['message']);
					if ($emoticons_active === true) {
						$comments[$i]['message'] = \ACP3\Modules\Emoticons\EmoticonsFunctions::emoticonsReplace($comments[$i]['message']);
					}
				}
				$this->injector['View']->assign('comments', $comments);
				$this->injector['View']->assign('can_delete', $can_delete);
			}
		}
	}

	public function actionSettings() {
		$emoticons_active = Core\Modules::isActive('emoticons');

		if (isset($_POST['submit']) === true) {
			if (empty($_POST['dateformat']) || ($_POST['dateformat'] !== 'long' && $_POST['dateformat'] !== 'short'))
				$errors['dateformat'] = $this->injector['Lang']->t('system', 'select_date_format');
			if ($emoticons_active === true && (!isset($_POST['emoticons']) || ($_POST['emoticons'] != 0 && $_POST['emoticons'] != 1)))
				$errors[] = $this->injector['Lang']->t('comments', 'select_emoticons');

			if (isset($errors) === true) {
				$this->injector['View']->assign('error_msg', Core\Functions::errorBox($errors));
			} elseif (Core\Validate::formToken() === false) {
				$this->injector['View']->setContent(Core\Functions::errorBox($this->injector['Lang']->t('system', 'form_already_submitted')));
			} else {
				$data = array(
					'dateformat' => Core\Functions::str_encode($_POST['dateformat']),
					'emoticons' => $_POST['emoticons'],
				);
				$bool = Core\Config::setSettings('comments', $data);

				$this->injector['Session']->unsetFormToken();

				Core\Functions::setRedirectMessage($bool, $this->injector['Lang']->t('system', $bool === true ? 'settings_success' : 'settings_error'), 'acp/comments');
			}
		}
		if (isset($_POST['submit']) === false || isset($errors) === true && is_array($errors) === true) {
			$settings = Core\Config::getSettings('comments');

			$this->injector['View']->assign('dateformat', $this->injector['Date']->dateformatDropdown($settings['dateformat']));

			// Emoticons erlauben
			if ($emoticons_active === true) {
				$lang_allow_emoticons = array($this->injector['Lang']->t('system', 'yes'), $this->injector['Lang']->t('system', 'no'));
				$this->injector['View']->assign('allow_emoticons', Core\Functions::selectGenerator('emoticons', array(1, 0), $lang_allow_emoticons, $settings['emoticons'], 'checked'));
			}

			$this->injector['Session']->generateFormToken();
		}
	}

}