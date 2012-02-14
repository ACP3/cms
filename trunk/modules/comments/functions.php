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

	$settings = config::getModuleSettings('comments');

	// Auflistung der Kommentare
	$comments = $db->query('SELECT u.nickname AS user_name, c.name, c.user_id, c.date, c.message FROM {pre}comments AS c LEFT JOIN ({pre}users AS u) ON u.id = c.user_id WHERE c.module = \'' . $module . '\' AND c.entry_id = \'' . $entry_id . '\' ORDER BY c.date ASC LIMIT ' . POS . ', ' . $auth->entries);
	$c_comments = count($comments);

	if ($c_comments > 0) {
		// Falls in den Moduleinstellungen aktiviert und Emoticons überhaupt aktiv sind, diese einbinden
		$emoticons_active = false;
		if ($settings['emoticons'] == 1) {
			$emoticons_active = modules::check('emoticons', 'functions') === true ? true : false;
			if ($emoticons_active === true) {
				require_once MODULES_DIR . 'emoticons/functions.php';
			}
		}

		$tpl->assign('pagination', pagination($db->countRows('*', 'comments', 'module = \'' . $module . '\' AND entry_id = \'' . $entry_id . '\'')));

		for ($i = 0; $i < $c_comments; ++$i) {
			if (empty($comments[$i]['user_name']) && empty($comments[$i]['name'])) {
				$comments[$i]['name'] = $lang->t('users', 'deleted_user');
				$comments[$i]['user_id'] = 0;
			}
			$comments[$i]['name'] = $db->escape(!empty($comments[$i]['user_name']) ? $comments[$i]['user_name'] : $comments[$i]['name'], 3);
			$comments[$i]['date'] = $date->format($comments[$i]['date'], $settings['dateformat']);
			$comments[$i]['message'] = str_replace(array("\r\n", "\r", "\n"), '<br />', $db->escape($comments[$i]['message'], 3));
			if ($emoticons_active === true) {
				$comments[$i]['message'] = emoticonsReplace($comments[$i]['message']);
			}
		}
		$tpl->assign('comments', $comments);
	}

	$content = view::fetchTemplate('comments/list.tpl');

	if (modules::check('comments', 'create') === true) {
		require_once MODULES_DIR . 'comments/create.php';
		$content.= commentsCreate($module, $entry_id);
	}

	return $content;
}