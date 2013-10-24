<?php

namespace ACP3\Modules\Comments;

use ACP3\Core;

/**
 * Description of CommentsFrontend
 *
 * @author Tino Goratsch
 */
class Frontend extends Core\ModuleController {

	private $module;
	private $entry_id;

	public function __construct($module, $entry_id) {
		parent::__construct();
		$this->module = $module;
		$this->entry_id = $entry_id;
	}

	public function actionCreate() {
		$captchaAccess = Core\Modules::hasPermission('captcha', 'image');

		// Formular für das Eintragen von Kommentaren
		if (isset($_POST['submit']) === true) {
			$ip = $_SERVER['REMOTE_ADDR'];

			// Flood Sperre
			$flood = $this->db->fetchColumn('SELECT MAX(date) FROM ' . DB_PRE . 'comments WHERE ip = ?', array($ip));
			if (!empty($flood)) {
				$flood_time = $this->date->timestamp($flood) + CONFIG_FLOOD;
			}
			$time = $this->date->timestamp();

			if (isset($flood_time) && $flood_time > $time)
				$errors[] = sprintf($this->lang->t('system', 'flood_no_entry_possible'), $flood_time - $time);
			if (empty($_POST['name']))
				$errors['name'] = $this->lang->t('system', 'name_to_short');
			if (strlen($_POST['message']) < 3)
				$errors['message'] = $this->lang->t('system', 'message_to_short');
			if ($captchaAccess === true && $this->auth->isUser() === false && Core\Validate::captcha($_POST['captcha']) === false)
				$errors['captcha'] = $this->lang->t('captcha', 'invalid_captcha_entered');

			if (isset($errors) === true) {
				$this->view->assign('error_msg', Core\Functions::errorBox($errors));
			} elseif (Core\Validate::formToken() === false) {
				return Core\Functions::errorBox($this->lang->t('system', 'form_already_submitted'));
			} else {
				$mod_id = $this->db->fetchColumn('SELECT id FROM ' . DB_PRE . 'modules WHERE name = ?', array($this->module));
				$insert_values = array(
					'id' => '',
					'date' => $this->date->getCurrentDateTime(),
					'ip' => $ip,
					'name' => Core\Functions::strEncode($_POST['name']),
					'user_id' => $this->auth->isUser() === true && Core\Validate::isNumber($this->auth->getUserId() === true) ? $this->auth->getUserId() : '',
					'message' => Core\Functions::strEncode($_POST['message']),
					'module_id' => $mod_id,
					'entry_id' => $this->entry_id,
				);

				$bool = $this->db->insert(DB_PRE . 'comments', $insert_values);

				$this->session->unsetFormToken();

				Core\Functions::setRedirectMessage($bool, $this->lang->t('system', $bool !== false ? 'create_success' : 'create_error'), $this->uri->query);
			}
		}
		if (isset($_POST['submit']) === false || isset($errors) === true && is_array($errors) === true) {
			$settings = Core\Config::getSettings('comments');

			// Emoticons einbinden, falls diese aktiv sind
			if ($settings['emoticons'] == 1 && Core\Modules::isActive('emoticons') === true) {
				// Emoticons im Formular anzeigen
				$this->view->assign('emoticons', \ACP3\Modules\Emoticons\Helpers::emoticonsList());
			}

			$defaults = array();

			// Falls Benutzer eingeloggt ist, Formular schon teilweise ausfüllen
			if ($this->auth->isUser() === true) {
				$user = $this->auth->getUserInfo();
				$disabled = ' readonly="readonly" class="readonly"';

				if (isset($_POST['submit'])) {
					$_POST['name'] = $user['nickname'];
					$_POST['name_disabled'] = $disabled;
				} else {
					$defaults['name'] = $user['nickname'];
					$defaults['name_disabled'] = $disabled;
					$defaults['message'] = '';
				}
			} else {
				$defaults['name'] = '';
				$defaults['name_disabled'] = '';
				$defaults['message'] = '';
			}
			$this->view->assign('form', isset($_POST['submit']) ? array_merge($defaults, $_POST) : $defaults);

			if ($captchaAccess === true) {
				$this->view->assign('captcha', \ACP3\Modules\Captcha\Helpers::captcha());
			}

			$this->session->generateFormToken();

			return $this->view->fetchTemplate('comments/create.tpl');
		}
	}

	public function actionList() {
		Core\Functions::getRedirectMessage();

		$settings = Core\Config::getSettings('comments');

		// Auflistung der Kommentare
		$comments = $this->db->fetchAll('SELECT u.nickname AS user_name, c.name, c.user_id, c.date, c.message FROM ' . DB_PRE . 'comments AS c JOIN ' . DB_PRE . 'modules AS m ON(m.id = c.module_id) LEFT JOIN (' . DB_PRE . 'users AS u) ON u.id = c.user_id WHERE m.name = ? AND c.entry_id = ? ORDER BY c.date ASC LIMIT ' . POS . ', ' . $this->auth->entries, array($this->module, $this->entry_id));
		$c_comments = count($comments);

		if ($c_comments > 0) {
			// Falls in den Moduleinstellungen aktiviert und Emoticons überhaupt aktiv sind, diese einbinden
			$emoticons_active = false;
			if ($settings['emoticons'] == 1) {
				$emoticons_active = Core\Modules::isActive('emoticons');
			}

			$this->view->assign('pagination', Core\Functions::pagination(Helpers::commentsCount($this->module, $this->entry_id)));

			for ($i = 0; $i < $c_comments; ++$i) {
				if (empty($comments[$i]['user_name']) && empty($comments[$i]['name'])) {
					$comments[$i]['name'] = $this->lang->t('users', 'deleted_user');
					$comments[$i]['user_id'] = 0;
				}
				$comments[$i]['name'] = !empty($comments[$i]['user_name']) ? $comments[$i]['user_name'] : $comments[$i]['name'];
				$comments[$i]['date_formatted'] = $this->date->format($comments[$i]['date'], $settings['dateformat']);
				$comments[$i]['date_iso'] = $this->date->format($comments[$i]['date'], 'c');
				$comments[$i]['message'] = Core\Functions::nl2p($comments[$i]['message']);
				if ($emoticons_active === true) {
					$comments[$i]['message'] = \ACP3\Modules\Emoticons\Helpers::emoticonsReplace($comments[$i]['message']);
				}
			}
			$this->view->assign('comments', $comments);
		}

		if (Core\Modules::hasPermission('comments', 'create') === true) {
			$this->view->assign('comments_create_form', $this->actionCreate());
		}

		return $this->view->fetchTemplate('comments/list.tpl');
	}

}