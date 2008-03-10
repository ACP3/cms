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
function comments_count($entry_id, $module = 0)
{
	global $db, $modules;

	$module = !empty($module) ? $module : $modules->mod;

	return $db->select('id', 'comments', 'module = \'' . $module . '\' AND entry_id =\'' . $entry_id . '\'', 0, 0, 0, 1);
}
/**
 * Gibt das Formular für die Kommentare aus
 *
 * @param string $url
 * 	Die URL, an welche das Formular abgesendet werden soll
 * @param string $module
 * 	Das jeweilige Modul
 * @param integer $entry_id
 * 	Die ID des jeweiligen Eintrages
 * @return string
 */
function comments_form($module = 0, $entry_id = 0)
{
	global $auth, $db, $modules, $tpl, $validate;

	if (isset($_POST['submit']) && isset($_POST['module']) && isset($_POST['entry_id']) && $validate->is_number($_POST['entry_id'])) {
		$ip = $_SERVER['REMOTE_ADDR'];
		$form = $_POST['form'];
		$module = $_POST['module'];
		$entry_id = $_POST['entry_id'];

		// Flood Sperre
		$flood = $db->select('date', 'comments', 'ip = \'' . $ip . '\'', 'id DESC', '1');
		if (count($flood) == '1') {
			$flood_time = $flood[0]['date'] + CONFIG_FLOOD;
		}
		$time = date_aligned(2, time());

		if (isset($flood_time) && $flood_time > $time)
			$errors[] = sprintf(lang('common', 'flood_no_entry_possible'), $flood_time - $time);
		if (empty($form['name']))
			$errors[] = lang('common', 'name_to_short');
		if (strlen($form['message']) < 3)
			$errors[] = lang('common', 'message_to_short');
		if (!$modules->check($db->escape($module, 2), 'list'))
			$errors[] = lang('comments', 'module_doesnt_exist');

		if (isset($errors)) {
			combo_box($errors);
		} else {
			$insert_values = array(
				'id' => '',
				'ip' => $ip,
				'date' => $time,
				'name' => $db->escape($form['name']),
				'user_id' => $auth->is_user() && preg_match('/\d/', $_SESSION['acp3_id']) ? $_SESSION['acp3_id'] : '',
				'message' => $db->escape($form['message']),
				'module' => $db->escape($module, 2),
				'entry_id' => $entry_id,
			);

			$bool = $db->insert('comments', $insert_values);

			return combo_box($bool ? lang('comments', 'create_success') : lang('comments', 'create_error'), uri($module . '/details/id_' . $entry_id));
		}
	}
	if (!isset($_POST['submit']) || isset($errors) && is_array($errors)) {
		$module = !empty($module) ? $module : $modules->mod;
		$entry_id = !empty($entry_id) ? $entry_id : $modules->id;

		$tpl->assign('com_form', array('module' => $module, 'entry_id' => $entry_id));

		//Falls aktiv, Emoticons anzeigen
		if ($modules->check('emoticons', 'functions')) {
			include_once 'modules/emoticons/functions.php';
			$tpl->assign('emoticons', emoticons_list());
		}

		return $tpl->fetch('comments/create.html');
	}
}
/**
 * Gibt eine Liste von Kommentaren aus
 *
 * @param string $module
 * 	Das jeweilige Modul
 * @param integer $entry_id
 * 	Die ID des jeweiligen Eintrages
 * @return string
 */
function comments_list($module = 0, $entry_id = 0)
{
	global $db, $modules, $tpl;

	$module = !empty($module) ? $module : $modules->mod;
	$entry_id = !empty($entry_id) ? $entry_id : $modules->id;
	$comments = $db->select('name, user_id, date, message', 'comments', 'module = \'' . $module . '\' AND entry_id = \'' . $entry_id . '\'', 'date ASC', POS, CONFIG_ENTRIES);
	$c_comments = count($comments);
	$emoticons = false;

	if ($modules->check('emoticons', 'functions')) {
		include_once 'modules/emoticons/functions.php';
		$emoticons = true;
	}

	if ($c_comments > 0) {
		$tpl->assign('pagination', pagination($db->select('id', 'comments', 'module = \'' . $module . '\' AND entry_id = \'' . $entry_id . '\'', 0, 0, 0, 1)));
		for ($i = 0; $i < $c_comments; $i++) {
			$comments[$i]['date'] = date_aligned(1, $comments[$i]['date']);
			if (empty($comments[$i]['user_id'])) {
				unset($comments[$i]['user_id']);
			}
			$comments[$i]['message'] = str_replace(array("\r\n", "\r", "\n"), '<br />', $comments[$i]['message']);
			if ($emoticons) {
				$comments[$i]['message'] = emoticons_replace($comments[$i]['message']);
			}
		}
		$tpl->assign('comments', $comments);

	}
	return $tpl->fetch('comments/list.html');
}
?>