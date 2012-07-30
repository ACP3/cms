<?php
/**
 * Static Pages
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
function setStaticPagesCache($id)
{
	global $db;
	return ACP3_Cache::create('static_pages_list_id_' . $id, $db->select('start, end, title, text', 'static_pages', 'id = \'' . $id . '\''));
}
/**
 * Bindet die gecachete statische Seite ein
 *
 * @param integer $id
 *  Die ID der statischen Seite
 * @return array
 */
function getStaticPagesCache($id)
{
	if (ACP3_Cache::check('static_pages_list_id_' . $id) === false)
		setStaticPagesCache($id);

	return ACP3_Cache::output('static_pages_list_id_' . $id);
}
/**
 * Liest alle statischen Seiten ein
 *
 * @param integer $id
 * @return array
 */
function staticPagesList($id = '')
{
	global $db;

	$static_pages = $db->select('id, start, end, title, text', 'static_pages');
	$c_static_pages = count($static_pages);

	if ($c_static_pages > 0) {
		for ($i = 0; $i < $c_static_pages; ++$i) {
			$static_pages[$i]['text'] = $db->escape($static_pages[$i]['text'], 3);
			$static_pages[$i]['selected'] = selectEntry('static_pages', $static_pages[$i]['id'], $id);
		}
	}
	return $static_pages;
}