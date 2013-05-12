<?php

namespace ACP3\Modules\Comments;

use ACP3\Core;

/**
 * Description of CommentsFrontend
 *
 * @author Tino
 */
class CommentsFrontend extends Core\ModuleController {

	public function __construct($injector) {
		parent::__construct($injector);
	}

	public function actionCreate($module, $entry_id) {
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
			if (Core\Modules::check($_POST['module'], 'list') === false || Core\Validate::isNumber($_POST['entry_id']) === false)
				$errors[] = $this->injector['Lang']->t('comments', 'module_doesnt_exist');
			if ($captchaAccess === true && $this->injector['Auth']->isUser() === false && Core\Validate::captcha($_POST['captcha']) === false)
				$errors['captcha'] = $this->injector['Lang']->t('captcha', 'invalid_captcha_entered');

			if (isset($errors) === true) {
				$this->injector['View']->assign('error_msg', Core\Functions::errorBox($errors));
			} elseif (Core\Validate::formToken() === false) {
				return Core\Functions::errorBox($this->injector['Lang']->t('system', 'form_already_submitted'));
			} else {
				$mod_id = $this->injector['Db']->fetchColumn('SELECT id FROM ' . DB_PRE . 'modules WHERE name = ?', array($_POST['module']));
				$insert_values = array(
					'id' => '',
					'date' => $this->injector['Date']->getCurrentDateTime(),
					'ip' => $ip,
					'name' => Core\Functions::str_encode($_POST['name']),
					'user_id' => $this->injector['Auth']->isUser() === true && Core\Validate::isNumber($this->injector['Auth']->getUserId() === true) ? $this->injector['Auth']->getUserId() : '',
					'message' => Core\Functions::str_encode($_POST['message']),
					'module_id' => $mod_id,
					'entry_id' => $_POST['entry_id'],
				);

				$bool = $this->injector['Db']->insert(DB_PRE . 'comments', $insert_values);

				$this->injector['Session']->unsetFormToken();

				Core\Functions::setRedirectMessage($bool, $this->injector['Lang']->t('system', $bool !== false ? 'create_success' : 'create_error'), $this->injector['URI']->query);
			}
		}
		if (isset($_POST['submit']) === false || isset($errors) === true && is_array($errors) === true) {
			$settings = Core\Config::getSettings('comments');

			// Emoticons einbinden, falls diese aktiv sind
			if (Core\Modules::check('emoticons', 'functions') === true && $settings['emoticons'] == 1) {
				require_once MODULES_DIR . 'emoticons/functions.php';

				// Emoticons im Formular anzeigen
				$this->injector['View']->assign('emoticons', emoticonsList());
			}

			// Name des Moduls und Datensatznummer ins Formular einbinden
			$defaults = array(
				'module' => $module,
				'entry_id' => $entry_id
			);

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

}