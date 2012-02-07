<?php
/**
 * Comments
 *
 * @author Goratsch Webdesign
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
	if (isset($_POST['form']) === true) {
		$ip = $_SERVER['REMOTE_ADDR'];
		$form = $_POST['form'];

		// Flood Sperre
		$flood = $db->select('date', 'comments', 'ip = \'' . $ip . '\'', 'id DESC', '1');
		if (count($flood) == '1') {
			$flood_time = $flood[0]['date'] + CONFIG_FLOOD;
		}
		$time = $date->timestamp();

		if (isset($flood_time) && $flood_time > $time)
			$errors[] = sprintf($lang->t('common', 'flood_no_entry_possible'), $flood_time - $time);
		if (empty($form['name']))
			$errors[] = $lang->t('common', 'name_to_short');
		if (strlen($form['message']) < 3)
			$errors[] = $lang->t('common', 'message_to_short');
		if (!modules::check($db->escape($form['module'], 2), 'list') === true || validate::isNumber($form['entry_id']) === false)
			$errors[] = $lang->t('comments', 'module_doesnt_exist');
		if ($auth->isUser() === false && validate::captcha($form['captcha']) === false)
			$errors[] = $lang->t('captcha', 'invalid_captcha_entered');

		if (isset($errors) === true) {
			$tpl->assign('error_msg', errorBox($errors));
		} elseif (validate::formToken() === false) {
			return errorBox($lang->t('common', 'form_already_submitted'));
		} else {
			$insert_values = array(
				'id' => '',
				'ip' => $ip,
				'date' => $time,
				'name' => $auth->isUser() && validate::isNumber($auth->getUserId()) ? '' : $db->escape($form['name']),
				'user_id' => $auth->isUser() && validate::isNumber($auth->getUserId()) ? $auth->getUserId() : '',
				'message' => $db->escape($form['message']),
				'module' => $db->escape($form['module'], 2),
				'entry_id' => $form['entry_id'],
			);

			$bool = $db->insert('comments', $insert_values);

			$session->unsetFormToken();

			return confirmBox($bool !== false ? $lang->t('common', 'create_success') : $lang->t('common', 'create_error'), $uri->route($uri->query));
		}
	}
	if (isset($_POST['form']) === false || isset($errors) === true && is_array($errors) === true) {
		// Emoticons einbinden, falls diese aktiv sind
		if (modules::check('emoticons', 'functions') === true) {
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
		if ($auth->isUser()) {
			$user = $auth->getUserInfo();
			$disabled = ' readonly="readonly" class="readonly"';

			if (isset($form)) {
				$form['name'] = $user['nickname'];
				$form['name_disabled'] = $disabled;
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
		$tpl->assign('form', isset($form) ? array_merge($defaults, $form) : $defaults);
		$tpl->assign('captcha', captcha());

		$session->generateFormToken();

		return view::fetchTemplate('comments/create.tpl');
	}
}
