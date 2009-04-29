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
 * @param string $module
 * 	Das jeweilige Modul
 * @param integer $entry_id
 * 	Die ID des jeweiligen Eintrages
 * @return integer
 */
function commentsCount($module, $entry_id)
{
	global $db;

	return $db->countRows('*', 'comments', 'module = \'' . $module . '\' AND entry_id =\'' . $entry_id . '\'');
}
/**
 * Zeigt alle Kommentare für das jeweilige Modul und Datensatz
 * Gibt das Formular für das Eintragen von Kommentaren aus
 *
 * @param string $module
 * 	Das jeweilige Modul
 * @param integer $entry_id
 * 	Die ID des jeweiligen Eintrages
 * @return string
 */
function commentsList($module, $entry_id)
{
	global $date, $db, $lang, $tpl;

	// Auflistung der Kommentare
	$comments = $db->query('SELECT IF(c.name != "" AND c.user_id = 0,c.name,u.nickname) AS name, c.user_id, c.date, c.message FROM ' . CONFIG_DB_PRE . 'comments AS c LEFT JOIN (' . CONFIG_DB_PRE . 'users AS u) ON u.id = c.user_id WHERE c.module = \'' . $module . '\' AND c.entry_id = \'' . $entry_id . '\' ORDER BY c.date ASC LIMIT ' . POS . ', ' . CONFIG_ENTRIES);
	$c_comments = count($comments);

	// Emoticons einbinden, falls diese aktiv sind
	$emoticons = false;
	if (modules::check('emoticons', 'functions') == 1) {
		require_once ACP3_ROOT . 'modules/emoticons/functions.php';
		$emoticons = true;
	}

	if ($c_comments > 0) {
		$tpl->assign('pagination', pagination($db->countRows('*', 'comments', 'module = \'' . $module . '\' AND entry_id = \'' . $entry_id . '\'')));
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

	$content = $tpl->fetch('comments/list.html');

	if (modules::check('comments', 'create') == 1) {
		require_once ACP3_ROOT . 'modules/comments/create.php';
		$content.= commentsCreate($module, $entry_id);
	}
	return $content;
}
?>