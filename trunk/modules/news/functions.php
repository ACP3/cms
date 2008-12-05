<?php
/**
 * News
 *
 * @author Goratsch Webdesign
 * @package ACP3
 * @subpackage Modules
 */
function setNewsCache($id)
{
	global $db;
	return cache::create('news_details_id_' . $id, $db->select('id, start, headline, text, readmore, comments, category_id, uri, target, link_title', 'news', 'id = \'' . $id . '\''));
}
function getNewsCache($id)
{
	if (!cache::check('news_details_id_' . $id))
		setNewsCache($id);

	return cache::output('news_details_id_' . $id);
}
?>
