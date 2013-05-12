<?php

/**
 * Comments
 *
 * @author Tino Goratsch
 * @package ACP3
 * @subpackage Modules
 */

namespace ACP3\Modules\Comments;

use ACP3\Core;

class CommentsFunctions {

	/**
	 * Zählt die Anzahl der Kommentare für einen bestimmten Eintrag eines Modules zusammen
	 *
	 * @param string $module
	 * 	Das jeweilige Modul
	 * @param integer $entry_id
	 * 	Die ID des jeweiligen Eintrages
	 * @return integer
	 */
	public static function commentsCount($module, $entry_id) {
		return \ACP3\CMS::$injector['Db']->fetchColumn('SELECT COUNT(*) FROM ' . DB_PRE . 'comments AS c JOIN ' . DB_PRE . 'modules AS m ON(m.id = c.module_id) WHERE m.name = ? AND c.entry_id = ?', array($module, $entry_id));
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
	public static function commentsList($module, $entry_id) {
		Core\Functions::getRedirectMessage();

		$settings = Core\Config::getSettings('comments');

		// Auflistung der Kommentare
		$comments = \ACP3\CMS::$injector['Db']->fetchAll('SELECT u.nickname AS user_name, c.name, c.user_id, c.date, c.message FROM ' . DB_PRE . 'comments AS c JOIN ' . DB_PRE . 'modules AS m ON(m.id = c.module_id) LEFT JOIN (' . DB_PRE . 'users AS u) ON u.id = c.user_id WHERE m.name = ? AND c.entry_id = ? ORDER BY c.date ASC LIMIT ' . POS . ', ' . \ACP3\CMS::$injector['Auth']->entries, array($module, $entry_id));
		$c_comments = count($comments);

		if ($c_comments > 0) {
			// Falls in den Moduleinstellungen aktiviert und Emoticons überhaupt aktiv sind, diese einbinden
			$emoticons_active = false;
			if ($settings['emoticons'] == 1) {
				$emoticons_active = Core\Modules::isActive('emoticons') === true ? true : false;
			}

			\ACP3\CMS::$injector['View']->assign('pagination', Core\Functions::pagination(self::commentsCount($module, $entry_id)));

			for ($i = 0; $i < $c_comments; ++$i) {
				if (empty($comments[$i]['user_name']) && empty($comments[$i]['name'])) {
					$comments[$i]['name'] = \ACP3\CMS::$injector['Lang']->t('users', 'deleted_user');
					$comments[$i]['user_id'] = 0;
				}
				$comments[$i]['name'] = !empty($comments[$i]['user_name']) ? $comments[$i]['user_name'] : $comments[$i]['name'];
				$comments[$i]['date_formatted'] = \ACP3\CMS::$injector['Date']->format($comments[$i]['date'], $settings['dateformat']);
				$comments[$i]['date_iso'] = \ACP3\CMS::$injector['Date']->format($comments[$i]['date'], 'c');
				$comments[$i]['message'] = Core\Functions::nl2p($comments[$i]['message']);
				if ($emoticons_active === true) {
					$comments[$i]['message'] = emoticonsReplace($comments[$i]['message']);
				}
			}
			\ACP3\CMS::$injector['View']->assign('comments', $comments);
		}

		if (Core\Modules::check('comments', 'create') === true) {
			require_once MODULES_DIR . 'comments/create.php';
			\ACP3\CMS::$injector['View']->assign('comments_create_form', commentsCreate($module, $entry_id));
		}

		return \ACP3\CMS::$injector['View']->fetchTemplate('comments/list.tpl');
	}

}