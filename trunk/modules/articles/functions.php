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
	global $db;
	return ACP3_Cache::create('articles_list_id_' . $id, $db->select('start, end, title, text', 'articles', 'id = \'' . $id . '\''));
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
	if (ACP3_Cache::check('articles_list_id_' . $id) === false)
		setArticlesCache($id);

	return ACP3_Cache::output('articles_list_id_' . $id);
}
/**
 * Liest alle statischen Seiten ein
 *
 * @param integer $id
 * @return array
 */
function articlesList($id = '')
{
	global $db;

	$articles = $db->select('id, start, end, title, text', 'articles', 0, 'title ASC');
	$c_articles = count($articles);

	if ($c_articles > 0) {
		for ($i = 0; $i < $c_articles; ++$i) {
			$articles[$i]['text'] = $db->escape($articles[$i]['text'], 3);
			$articles[$i]['selected'] = selectEntry('articles', $articles[$i]['id'], $id);
		}
	}
	return $articles;
}