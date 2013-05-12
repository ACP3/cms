<?php
/**
 * Articles
 *
 * @author Tino Goratsch
 * @package ACP3
 * @subpackage Modules
 */
/**
 * Erstellt den Cache einer statischen Seite anhand der angegebenen ID
 *
 * @param integer $id
 *  Die ID der statischen Seite
 * @return boolean
 */
function setArticlesCache($id)
{
	$data = ACP3\CMS::$injector['Db']->fetchAssoc('SELECT start, end, title, text FROM ' . DB_PRE . 'articles WHERE id = ?', array($id));
	return ACP3\Core\Cache::create('list_id_' . $id, $data, 'articles');
}
/**
 * Bindet die gecachete statische Seite ein
 *
 * @param integer $id
 *  Die ID der statischen Seite
 * @return array
 */
function getArticlesCache($id)
{
	if (ACP3\Core\Cache::check('list_id_' . $id, 'articles') === false)
		setArticlesCache($id);

	return ACP3\Core\Cache::output('list_id_' . $id, 'articles');
}
/**
 * Liest alle statischen Seiten ein
 *
 * @param integer $id
 * @return array
 */
function articlesList($id = '')
{
	$articles = ACP3\CMS::$injector['Db']->fetchAll('SELECT id, start, end, title, text FROM ' . DB_PRE . 'articles ORDER BY title ASC');
	$c_articles = count($articles);

	if ($c_articles > 0) {
		for ($i = 0; $i < $c_articles; ++$i) {
			$articles[$i]['selected'] = ACP3\Core\Functions::selectEntry('articles', $articles[$i]['id'], $id);
		}
	}
	return $articles;
}