<?php
/**
 * Comments
 *
 * @author Goratsch Webdesign
 * @package ACP3
 * @subpackage Modules
 */
/**
 * Zählt die Anzahl der Kommentare für einen bestimmten Eintrag eines Modules zusammen
 *
 * @param integer $entry_id
 * 	Die ID des jeweiligen Eintrages
 * @param string $module
 * 	Das jeweilige Modul
 * @return integer
 */
function commentsCount($entry_id, $module)
{
	global $db;

	return $db->select('COUNT(id)', 'comments', 'module = \'' . $module . '\' AND entry_id =\'' . $entry_id . '\'', 0, 0, 0, 1);
}
/**
 * Zeigt alle Kommentare für das jeweilige Modul und Datensatz
 * Gibt das Formular für das Eintragen von Kommentaren aus
 *
 * @param string $url
 * 	Die URL, an welche das Formular abgesendet werden soll
 * @param string $module
 * 	Das jeweilige Modul
 * @param integer $entry_id
 * 	Die ID des jeweiligen Eintrages
 * @return string
 */
function comments($module, $entry_id)
{
	global $auth, $date, $db, $lang, $uri, $tpl;

	// Formular für das Eintragen von Kommentaren
	if (isset($_POST['submit'])) {
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
		if (!modules::check($db->escape($form['module'], 2), 'list') || !validate::isNumber($form['entry_id']))
			$errors[] = $lang->t('comments', 'module_doesnt_exist');
		if (!$auth->isUser() && !validate::captcha($form['captcha'], $form['hash']))
			$errors[] = $lang->t('captcha', 'invalid_captcha_entered');

		if (isset($errors)) {
			$tpl->assign('error_msg', comboBox($errors));
		} else {
			$insert_values = array(
				'id' => '',
				'ip' => $ip,
				'date' => $time,
				'name' => $auth->isUser() && validate::isNumber(USER_ID) ? '' : $db->escape($form['name']),
				'user_id' => $auth->isUser() && validate::isNumber(USER_ID) ? USER_ID : '',
				'message' => $db->escape($form['message']),
				'module' => $db->escape($form['module'], 2),
				'entry_id' => $form['entry_id'],
			);

			$bool = $db->insert('comments', $insert_values);

			return comboBox($bool ? $lang->t('common', 'create_success') : $lang->t('common', 'create_error'), uri($uri->query));
		}
	}
	if (!isset($_POST['submit']) || isset($errors) && is_array($errors)) {
		// Auflistung der Kommentare
		$comments = $db->query('SELECT IF(u.nickname = NULL,c.name,u.nickname) AS name, c.user_id, c.date, c.message FROM ' . CONFIG_DB_PRE . 'comments AS c LEFT JOIN (' . CONFIG_DB_PRE . 'users AS u) ON u.id = c.user_id AND c.module = \'' . $module . '\' AND c.entry_id = \'' . $entry_id . '\' ORDER BY c.date ASC LIMIT ' . POS . ', ' . CONFIG_ENTRIES);
		$c_comments = count($comments);

		// Emoticons einbinden, falls diese aktiv sind
		$emoticons = false;
		if (modules::check('emoticons', 'functions')) {
			include_once ACP3_ROOT . 'modules/emoticons/functions.php';
			$emoticons = true;

			// Emoticons im Formular anzeigen
			$tpl->assign('emoticons', emoticonsList());
		}

		if ($c_comments > 0) {
			$tpl->assign('pagination', pagination($db->select('COUNT(id)', 'comments', 'module = \'' . $module . '\' AND entry_id = \'' . $entry_id . '\'', 0, 0, 0, 1)));
			for ($i = 0; $i < $c_comments; ++$i) {
				if (!empty($comments[$i]['user_id']) && empty($comments[$i]['name'])) {
					$comments[$i]['name'] = $lang->t('users', 'deleted_user');
					$comments[$i]['user_id'] = 0;
				}
				$comments[$i]['date'] = $date->format($comments[$i]['date']);
				$comments[$i]['message'] = str_replace(array("\r\n", "\r", "\n"), '<br />', $comments[$i]['message']);
				if ($emoticons) {
					$comments[$i]['message'] = emoticonsReplace($comments[$i]['message']);
				}
			}
			$tpl->assign('comments', $comments);
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

		return $tpl->fetch('comments/list.html');
	}
}
?>