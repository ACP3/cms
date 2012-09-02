<?php
/**
 * Comments
 *
 * @author Tino Goratsch
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

	return ACP3_CMS::$db->query('SELECT COUNT(*) FROM {pre}comments AS c JOIN {pre}modules AS m ON(m.id = c.module_id) WHERE m.name = \'' . $module . '\' AND c.entry_id =\'' . $entry_id . '\'', 1);
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

	$settings = ACP3_Config::getSettings('comments');

	// Auflistung der Kommentare
	$comments = ACP3_CMS::$db->query('SELECT u.nickname AS user_name, c.name, c.user_id, c.date, c.message FROM {pre}comments AS c JOIN {pre}modules AS m ON(m.id = c.module_id) LEFT JOIN ({pre}users AS u) ON u.id = c.user_id WHERE m.name = \'' . $module . '\' AND c.entry_id = \'' . $entry_id . '\' ORDER BY c.date ASC LIMIT ' . POS . ', ' . ACP3_CMS::$auth->entries);
	$c_comments = count($comments);

	if ($c_comments > 0) {
		// Falls in den Moduleinstellungen aktiviert und Emoticons überhaupt aktiv sind, diese einbinden
		$emoticons_active = false;
		if ($settings['emoticons'] == 1) {
			$emoticons_active = ACP3_Modules::check('emoticons', 'functions') === true ? true : false;
			if ($emoticons_active === true) {
				require_once MODULES_DIR . 'emoticons/functions.php';
			}
		}

		ACP3_CMS::$view->assign('pagination', pagination(commentsCount($module, $entry_id)));

		for ($i = 0; $i < $c_comments; ++$i) {
			if (empty($comments[$i]['user_name']) && empty($comments[$i]['name'])) {
				$comments[$i]['name'] = ACP3_CMS::$lang->t('users', 'deleted_user');
				$comments[$i]['user_id'] = 0;
			}
			$comments[$i]['name'] = ACP3_CMS::$db->escape(!empty($comments[$i]['user_name']) ? $comments[$i]['user_name'] : $comments[$i]['name'], 3);
			$comments[$i]['date'] = ACP3_CMS::$date->format($comments[$i]['date'], $settings['dateformat']);
			$comments[$i]['message'] = nl2p(ACP3_CMS::$db->escape($comments[$i]['message'], 3));
			if ($emoticons_active === true) {
				$comments[$i]['message'] = emoticonsReplace($comments[$i]['message']);
			}
		}
		ACP3_CMS::$view->assign('comments', $comments);
	}

	if (ACP3_Modules::check('comments', 'create') === true) {
		require_once MODULES_DIR . 'comments/create.php';
		ACP3_CMS::$view->assign('comments_create_form', commentsCreate($module, $entry_id));
	}

	return ACP3_CMS::$view->fetchTemplate('comments/list.tpl');
}