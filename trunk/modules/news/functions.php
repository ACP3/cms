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
	global $db;
	return ACP3_Cache::create('news_details_id_' . $id, $db->select('id, start, headline, text, readmore, comments, category_id, uri, target, link_title', 'news', 'id = \'' . $id . '\''));
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
	if (ACP3_Cache::check('news_details_id_' . $id) === false)
		setNewsCache($id);

	return ACP3_Cache::output('news_details_id_' . $id);
}