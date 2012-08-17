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
	global $auth, $date, $db, $lang, $session, $uri, $tpl;

	// Formular für das Eintragen von Kommentaren
	if (isset($_POST['submit']) === true) {
		$ip = $_SERVER['REMOTE_ADDR'];

		// Flood Sperre
		$flood = $db->select('date', 'comments', 'ip = \'' . $ip . '\'', 'id DESC', '1');
		if (count($flood) === 1) {
			$flood_time = $date->timestamp($flood[0]['date']) + CONFIG_FLOOD;
		}
		$time = $date->timestamp();

		if (isset($flood_time) && $flood_time > $time)
			$errors[] = sprintf($lang->t('common', 'flood_no_entry_possible'), $flood_time - $time);
		if (empty($_POST['name']))
			$errors['name'] = $lang->t('common', 'name_to_short');
		if (strlen($_POST['message']) < 3)
			$errors['message'] = $lang->t('common', 'message_to_short');
		if (ACP3_Modules::check($db->escape($_POST['module'], 2), 'list') === false || ACP3_Validate::isNumber($_POST['entry_id']) === false)
			$errors[] = $lang->t('comments', 'module_doesnt_exist');
		if ($auth->isUser() === false && ACP3_Validate::captcha($_POST['captcha']) === false)
			$errors['captcha'] = $lang->t('captcha', 'invalid_captcha_entered');

		if (isset($errors) === true) {
			$tpl->assign('error_msg', errorBox($errors));
		} elseif (ACP3_Validate::formToken() === false) {
			return errorBox($lang->t('common', 'form_already_submitted'));
		} else {
			$insert_values = array(
				'id' => '',
				'date' => $date->timestampToDateTime($time),
				'ip' => $ip,
				'name' => $auth->isUser() === true && ACP3_Validate::isNumber($auth->getUserId() === true) ? '' : $db->escape($_POST['name']),
				'user_id' => $auth->isUser() === true && ACP3_Validate::isNumber($auth->getUserId() === true) ? $auth->getUserId() : '',
				'message' => $db->escape($_POST['message']),
				'module' => $db->escape($_POST['module'], 2),
				'entry_id' => $_POST['entry_id'],
			);

			$bool = $db->insert('comments', $insert_values);

			$session->unsetFormToken();

			return confirmBox($lang->t('common', $bool !== false ? 'create_success' : 'create_error'), $uri->route($uri->query));
		}
	}
	if (isset($_POST['submit']) === false || isset($errors) === true && is_array($errors) === true) {
		$settings = ACP3_Config::getModuleSettings('comments');

		// Emoticons einbinden, falls diese aktiv sind
		if (ACP3_Modules::check('emoticons', 'functions') === true && $settings['emoticons'] == 1) {
			require_once MODULES_DIR . 'emoticons/functions.php';

			// Emoticons im Formular anzeigen
			$tpl->assign('emoticons', emoticonsList());
		}

		// Name des Moduls und Datensatznummer ins Formular einbinden
		$defaults = array(
			'module' => $module,
			'entry_id' => $entry_id
		);

		// Falls Benutzer eingeloggt ist, Formular schon teilweise ausfüllen
		if ($auth->isUser() === true) {
			$user = $auth->getUserInfo();
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
		$tpl->assign('form', isset($_POST['submit']) ? array_merge($defaults, $_POST) : $defaults);

		require_once MODULES_DIR . 'captcha/functions.php';
		$tpl->assign('captcha', captcha());

		$session->generateFormToken();

		return ACP3_View::fetchTemplate('comments/create.tpl');
	}
}