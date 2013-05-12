<?php

/**
 * Files
 *
 * @author Tino Goratsch
 * @package ACP3
 * @subpackage Modules
 */

namespace ACP3\Modules\Files;

use ACP3\Core\Cache;

class FilesFunctions {

	/**
	 * Setzt das Cache für einen Download
	 *
	 * @param integer $id
	 * 	Die ID des zu cachenden Download
	 * @return boolean
	 */
	public static function setFilesCache($id) {
		$data = \ACP3\CMS::$injector['Db']->fetchAssoc('SELECT f.id, f.start, f.category_id, f.file, f.size, f.title, f.text, f.comments, c.title AS category_name FROM ' . DB_PRE . 'files AS f, ' . DB_PRE . 'categories AS c WHERE f.id = ? AND f.category_id = c.id', array($id));
		return Cache::create('details_id_' . $id, $data, 'files');
	}

	/**
	 * Gibt den Cache eines Downloads aus
	 *
	 * @param integer $id
	 * 	ID des Downloads
	 * @return array
	 */
	public static function getFilesCache($id) {
		if (Cache::check('details_id_' . $id, 'files') === false)
			self::setFilesCache($id);

		return Cache::output('details_id_' . $id, 'files');
	}

}