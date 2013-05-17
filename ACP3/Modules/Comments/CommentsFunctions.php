<?php

/**
 * Comments
 *
 * @author Tino Goratsch
 * @package ACP3
 * @subpackage Modules
 */

namespace ACP3\Modules\Comments;

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
		return \ACP3\Core\Registry::get('Db')->fetchColumn('SELECT COUNT(*) FROM ' . DB_PRE . 'comments AS c JOIN ' . DB_PRE . 'modules AS m ON(m.id = c.module_id) WHERE m.name = ? AND c.entry_id = ?', array($module, $entry_id));
	}
}