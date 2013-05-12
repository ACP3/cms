<?php
/**
 * Comments
 *
 * @author Tino Goratsch
 * @package ACP3
 * @subpackage Modules
 */
/**
 * Gibt das Formular zum Erzeugen eines Kommentares aus
 *
 * @param string $module
 * 	Das jeweilige Modul
 * @param integer $entry_id
 * 	Die ID des jeweiligen Eintrages
 * @return string
 */
function commentsCreate($module, $entry_id)
{
	$captchaAccess = ACP3\Core\Modules::check('captcha', 'functions');

	// Formular für das Eintragen von Kommentaren
	if (isset($_POST['submit']) === true) {
		$ip = $_SERVER['REMOTE_ADDR'];

		// Flood Sperre
		$flood = ACP3\CMS::$injector['Db']->fetchColumn('SELECT MAX(date) FROM ' . DB_PRE . 'comments WHERE ip = ?', array($ip));
		if (!empty($flood)) {
			$flood_time = ACP3\CMS::$injector['Date']->timestamp($flood) + CONFIG_FLOOD;
		}
		$time = ACP3\CMS::$injector['Date']->timestamp();

		if (isset($flood_time) && $flood_time > $time)
			$errors[] = sprintf(ACP3\CMS::$injector['Lang']->t('system', 'flood_no_entry_possible'), $flood_time - $time);
		if (empty($_POST['name']))
			$errors['name'] = ACP3\CMS::$injector['Lang']->t('system', 'name_to_short');
		if (strlen($_POST['message']) < 3)
			$errors['message'] = ACP3\CMS::$injector['Lang']->t('system', 'message_to_short');
		if (ACP3\Core\Modules::check($_POST['module'], 'list') === false || ACP3\Core\Validate::isNumber($_POST['entry_id']) === false)
			$errors[] = ACP3\CMS::$injector['Lang']->t('comments', 'module_doesnt_exist');
		if ($captchaAccess === true && ACP3\CMS::$injector['Auth']->isUser() === false && ACP3\Core\Validate::captcha($_POST['captcha']) === false)
			$errors['captcha'] = ACP3\CMS::$injector['Lang']->t('captcha', 'invalid_captcha_entered');

		if (isset($errors) === true) {
			ACP3\CMS::$injector['View']->assign('error_msg', Core\Functions::errorBox($errors));
		} elseif (ACP3\Core\Validate::formToken() === false) {
			return Core\Functions::errorBox(ACP3\CMS::$injector['Lang']->t('system', 'form_already_submitted'));
		} else {
			$mod_id = ACP3\CMS::$injector['Db']->fetchColumn('SELECT id FROM ' . DB_PRE . 'modules WHERE name = ?', array($_POST['module']));
			$insert_values = array(
				'id' => '',
				'date' => ACP3\CMS::$injector['Date']->getCurrentDateTime(),
				'ip' => $ip,
				'name' => ACP3\Core\Functions::str_encode($_POST['name']),
				'user_id' => ACP3\CMS::$injector['Auth']->isUser() === true && ACP3\Core\Validate::isNumber(ACP3\CMS::$injector['Auth']->getUserId() === true) ? ACP3\CMS::$injector['Auth']->getUserId() : '',
				'message' => ACP3\Core\Functions::str_encode($_POST['message']),
				'module_id' => $mod_id,
				'entry_id' => $_POST['entry_id'],
			);

			$bool = ACP3\CMS::$injector['Db']->insert(DB_PRE . 'comments', $insert_values);

			ACP3\CMS::$injector['Session']->unsetFormToken();

			ACP3\Core\Functions::setRedirectMessage($bool, ACP3\CMS::$injector['Lang']->t('system', $bool !== false ? 'create_success' : 'create_error'), ACP3\CMS::$injector['URI']->query);
		}
	}
	if (isset($_POST['submit']) === false || isset($errors) === true && is_array($errors) === true) {
		$settings = ACP3\Core\Config::getSettings('comments');

		// Emoticons einbinden, falls diese aktiv sind
		if (ACP3\Core\Modules::check('emoticons', 'functions') === true && $settings['emoticons'] == 1) {
			require_once MODULES_DIR . 'emoticons/functions.php';

			// Emoticons im Formular anzeigen
			ACP3\CMS::$injector['View']->assign('emoticons', emoticonsList());
		}

		// Name des Moduls und Datensatznummer ins Formular einbinden
		$defaults = array(
			'module' => $module,
			'entry_id' => $entry_id
		);

		// Falls Benutzer eingeloggt ist, Formular schon teilweise ausfüllen
		if (ACP3\CMS::$injector['Auth']->isUser() === true) {
			$user = ACP3\CMS::$injector['Auth']->getUserInfo();
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
		ACP3\CMS::$injector['View']->assign('form', isset($_POST['submit']) ? array_merge($defaults, $_POST) : $defaults);

		if ($captchaAccess === true) {
			require_once MODULES_DIR . 'captcha/functions.php';
			ACP3\CMS::$injector['View']->assign('captcha', captcha());
		}

		ACP3\CMS::$injector['Session']->generateFormToken();

		return ACP3\CMS::$injector['View']->fetchTemplate('comments/create.tpl');
	}
}