<?php
/**
 * News
 *
 * @author Tino Goratsch
 * @package ACP3
 * @subpackage Modules
 */

namespace ACP3\Modules\News;

use ACP3\Core\Cache;

/**
 * Stellt einige Helperfunktionen bereit
 */
abstract class NewsHelpers {

	/**
	 * Erstellt den Cache einer News anhand der angegebenen ID
	 *
	 * @param integer $id
	 *  Die ID der News
	 * @return boolean
	 */
	public static function setNewsCache($id)
	{
		$data = \ACP3\Core\Registry::get('Db')->fetchAssoc('SELECT id, start, title, text, readmore, comments, category_id, uri, target, link_title FROM ' . DB_PRE . 'news WHERE id = ?', array($id));
		return Cache::create('details_id_' . $id, $data, 'news');
	}

	/**
	 * Bindet die gecachete News ein
	 *
	 * @param integer $id
	 *  Die ID der News
	 * @return array
	 */
	public static function getNewsCache($id)
	{
		if (Cache::check('details_id_' . $id, 'news') === false)
			self::setNewsCache($id);

		return Cache::output('details_id_' . $id, 'news');
	}

}
