<?php

/**
 * Articles
 *
 * @author Tino Goratsch
 * @package ACP3
 * @subpackage Modules
 */

namespace ACP3\Modules\Articles;

use ACP3\Core;

abstract class ArticlesFunctions {

	/**
	 * Erstellt den Cache eines Artikels anhand der angegebenen ID
	 *
	 * @param integer $id
	 *  Die ID der statischen Seite
	 * @return boolean
	 */
	public static function setArticlesCache($id)
	{
		$data = Core\Registry::get('Db')->fetchAssoc('SELECT start, end, title, text FROM ' . DB_PRE . 'articles WHERE id = ?', array($id));
		return Core\Cache::create('list_id_' . $id, $data, 'articles');
	}

	/**
	 * Bindet den gecacheten Artikel ein
	 *
	 * @param integer $id
	 *  Die ID der statischen Seite
	 * @return array
	 */
	public static function getArticlesCache($id)
	{
		if (Core\Cache::check('list_id_' . $id, 'articles') === false)
			self::setArticlesCache($id);

		return Core\Cache::output('list_id_' . $id, 'articles');
	}

	/**
	 * Gibt alle angelegten Artikel zurück
	 *
	 * @param integer $id
	 * @return array
	 */
	public static function articlesList($id = '')
	{
		$articles = Core\Registry::get('Db')->fetchAll('SELECT id, start, end, title, text FROM ' . DB_PRE . 'articles ORDER BY title ASC');
		$c_articles = count($articles);

		if ($c_articles > 0) {
			for ($i = 0; $i < $c_articles; ++$i) {
				$articles[$i]['selected'] = Core\Functions::selectEntry('articles', $articles[$i]['id'], $id);
			}
		}
		return $articles;
	}

}
