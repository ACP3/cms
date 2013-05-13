<?php

namespace ACP3\Modules\Comments;

use ACP3\Core;

/**
 * Description of CommentsFrontend
 *
 * @author Tino
 */
class CommentsFrontend extends Core\ModuleController {

	private $module;
	private $entry_id;

	public function __construct($injector, $module, $entry_id) {
		parent::__construct($injector);
		$this->module = $module;
		$this->entry_id = $entry_id;
	}

	public function actionCreate() {
		$captchaAccess = Core\Modules::check('captcha', 'image');

		// Formular für das Eintragen von Kommentaren
		if (isset($_POST['submit']) === true) {
			$ip = $_SERVER['REMOTE_ADDR'];

			// Flood Sperre
			$flood = $this->injector['Db']->fetchColumn('SELECT MAX(date) FROM ' . DB_PRE . 'comments WHERE ip = ?', array($ip));
			if (!empty($flood)) {
				$flood_time = $this->injector['Date']->timestamp($flood) + CONFIG_FLOOD;
			}
			$time = $this->injector['Date']->timestamp();

			if (isset($flood_time) && $flood_time > $time)
				$errors[] = sprintf($this->injector['Lang']->t('system', 'flood_no_entry_possible'), $flood_time - $time);
			if (empty($_POST['name']))
				$errors['name'] = $this->injector['Lang']->t('system', 'name_to_short');
			if (strlen($_POST['message']) < 3)
				$errors['message'] = $this->injector['Lang']->t('system', 'message_to_short');
			if ($captchaAccess === true && $this->injector['Auth']->isUser() === false && Core\Validate::captcha($_POST['captcha']) === false)
				$errors['captcha'] = $this->injector['Lang']->t('captcha', 'invalid_captcha_entered');

			if (isset($errors) === true) {
				$this->injector['View']->assign('error_msg', Core\Functions::errorBox($errors));
			} elseif (Core\Validate::formToken() === false) {
				return Core\Functions::errorBox($this->injector['Lang']->t('system', 'form_already_submitted'));
			} else {
				$mod_id = $this->injector['Db']->fetchColumn('SELECT id FROM ' . DB_PRE . 'modules WHERE name = ?', array($this->module));
				$insert_values = array(
					'id' => '',
					'date' => $this->injector['Date']->getCurrentDateTime(),
					'ip' => $ip,
					'name' => Core\Functions::str_encode($_POST['name']),
					'user_id' => $this->injector['Auth']->isUser() === true && Core\Validate::isNumber($this->injector['Auth']->getUserId() === true) ? $this->injector['Auth']->getUserId() : '',
					'message' => Core\Functions::str_encode($_POST['message']),
					'module_id' => $mod_id,
					'entry_id' => $this->entry_id,
				);

				$bool = $this->injector['Db']->insert(DB_PRE . 'comments', $insert_values);

				$this->injector['Session']->unsetFormToken();

				Core\Functions::setRedirectMessage($bool, $this->injector['Lang']->t('system', $bool !== false ? 'create_success' : 'create_error'), $this->injector['URI']->query);
			}
		}
		if (isset($_POST['submit']) === false || isset($errors) === true && is_array($errors) === true) {
			$settings = Core\Config::getSettings('comments');

			// Emoticons einbinden, falls diese aktiv sind
			if ($settings['emoticons'] == 1 && Core\Modules::isActive('emoticons') === true) {
				// Emoticons im Formular anzeigen
				$this->injector['View']->assign('emoticons', \ACP3\Modules\Emoticons\EmoticonsFunctions::emoticonsList());
			}

			$defaults = array();

			// Falls Benutzer eingeloggt ist, Formular schon teilweise ausfüllen
			if ($this->injector['Auth']->isUser() === true) {
				$user = $this->injector['Auth']->getUserInfo();
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
			$this->injector['View']->assign('form', isset($_POST['submit']) ? array_merge($defaults, $_POST) : $defaults);

			if ($captchaAccess === true) {
				$this->injector['View']->assign('captcha', \ACP3\Modules\Captcha\CaptchaFunctions::captcha());
			}

			$this->injector['Session']->generateFormToken();

			return $this->injector['View']->fetchTemplate('comments/create.tpl');
		}
	}

	public function actionList() {
		Core\Functions::getRedirectMessage();

		$settings = Core\Config::getSettings('comments');

		// Auflistung der Kommentare
		$comments = $this->injector['Db']->fetchAll('SELECT u.nickname AS user_name, c.name, c.user_id, c.date, c.message FROM ' . DB_PRE . 'comments AS c JOIN ' . DB_PRE . 'modules AS m ON(m.id = c.module_id) LEFT JOIN (' . DB_PRE . 'users AS u) ON u.id = c.user_id WHERE m.name = ? AND c.entry_id = ? ORDER BY c.date ASC LIMIT ' . POS . ', ' . $this->injector['Auth']->entries, array($this->module, $this->entry_id));
		$c_comments = count($comments);

		if ($c_comments > 0) {
			// Falls in den Moduleinstellungen aktiviert und Emoticons überhaupt aktiv sind, diese einbinden
			$emoticons_active = false;
			if ($settings['emoticons'] == 1) {
				$emoticons_active = Core\Modules::isActive('emoticons');
			}

			$this->injector['View']->assign('pagination', Core\Functions::pagination(CommentsFunctions::commentsCount($this->module, $this->entry_id)));

			for ($i = 0; $i < $c_comments; ++$i) {
				if (empty($comments[$i]['user_name']) && empty($comments[$i]['name'])) {
					$comments[$i]['name'] = $this->injector['Lang']->t('users', 'deleted_user');
					$comments[$i]['user_id'] = 0;
				}
				$comments[$i]['name'] = !empty($comments[$i]['user_name']) ? $comments[$i]['user_name'] : $comments[$i]['name'];
				$comments[$i]['date_formatted'] = $this->injector['Date']->format($comments[$i]['date'], $settings['dateformat']);
				$comments[$i]['date_iso'] = $this->injector['Date']->format($comments[$i]['date'], 'c');
				$comments[$i]['message'] = Core\Functions::nl2p($comments[$i]['message']);
				if ($emoticons_active === true) {
					$comments[$i]['message'] = \ACP3\Modules\Emoticons\EmoticonsFunctions::emoticonsReplace($comments[$i]['message']);
				}
			}
			$this->injector['View']->assign('comments', $comments);
		}

		if (Core\Modules::check('comments', 'create') === true) {
			$this->injector['View']->assign('comments_create_form', $this->actionCreate());
		}

		return $this->injector['View']->fetchTemplate('comments/list.tpl');
	}

}