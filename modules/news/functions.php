<?php
/**
 * News
 *
 * @author Tino Goratsch
 * @package ACP3
 * @subpackage Modules
 */
/**
 * Erstellt den Cache einer News anhand der angegebenen ID
 *
 * @param integer $id
 *  Die ID der News
 * @return boolean
 */
function setNewsCache($id)
{
	$data = ACP3_CMS::$db2->fetchAssoc('SELECT id, start, title, text, readmore, comments, category_id, uri, target, link_title FROM ' . DB_PRE . 'news WHERE id = ?', array($id));
	return ACP3_Cache::create('details_id_' . $id, $data, 'news');
}
/**
 * Bindet die gecachete News ein
 *
 * @param integer $id
 *  Die ID der News
 * @return array
 */
function getNewsCache($id)
{
	if (ACP3_Cache::check('details_id_' . $id, 'news') === false)
		setNewsCache($id);

	return ACP3_Cache::output('details_id_' . $id, 'news');
}