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
	global $auth, $date, $db, $lang, $tpl;

	// Auflistung der Kommentare
	$comments = $db->query('SELECT u.nickname AS user_name, c.name, c.user_id, c.date, c.message FROM ' . $db->prefix . 'comments AS c LEFT JOIN (' . $db->prefix . 'users AS u) ON u.id = c.user_id WHERE c.module = \'' . $module . '\' AND c.entry_id = \'' . $entry_id . '\' ORDER BY c.date ASC LIMIT ' . POS . ', ' . $auth->entries);
	$c_comments = count($comments);

	// Emoticons einbinden, falls diese aktiv sind
	$emoticons = modules::check('emoticons', 'functions') == 1 ? true : false;
	if ($emoticons) {
		require_once ACP3_ROOT . 'modules/emoticons/functions.php';
	}

	if ($c_comments > 0) {
		$tpl->assign('pagination', pagination($db->countRows('*', 'comments', 'module = \'' . $module . '\' AND entry_id = \'' . $entry_id . '\'')));

		$settings = config::output('comments');

		for ($i = 0; $i < $c_comments; ++$i) {
			if (empty($comments[$i]['user_name']) && empty($comments[$i]['name'])) {
				$comments[$i]['name'] = $lang->t('users', 'deleted_user');
				$comments[$i]['user_id'] = 0;
			}
			$comments[$i]['name'] = $db->escape(!empty($comments[$i]['user_name']) ? $comments[$i]['user_name'] : $comments[$i]['name'], 3);
			$comments[$i]['date'] = $date->format($comments[$i]['date'], $settings['dateformat']);
			$comments[$i]['message'] = str_replace(array("\r\n", "\r", "\n"), '<br />', $db->escape($comments[$i]['message'], 3));
			if ($emoticons) {
				$comments[$i]['message'] = emoticonsReplace($comments[$i]['message']);
			}
		}
		$tpl->assign('comments', $comments);
	}

	$content = modules::fetchTemplate('comments/list.html');

	if (modules::check('comments', 'create') == 1) {
		require_once ACP3_ROOT . 'modules/comments/create.php';
		$content.= commentsCreate($module, $entry_id);
	}

	return $content;
}
