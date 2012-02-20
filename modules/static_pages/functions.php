<?php
/**
 * Static Pages
 *
 * @author Goratsch Webdesign
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
	return cache::create('static_pages_list_id_' . $id, $db->select('start, end, title, text', 'static_pages', 'id = \'' . $id . '\''));
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
	if (cache::check('static_pages_list_id_' . $id) === false)
		setStaticPagesCache($id);

	return cache::output('static_pages_list_id_' . $id);
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
/**
 * Liest aus einem String alle vorhandenen HTML-Attribute ein und
 * liefert diese als assoziatives Array zurück
 *
 * @param string $string
 * @return array 
 */
function getHtmlAttributes($string)
{
	$matches = array();
	preg_match_all('/([\w:-]+)[\s]?=[\s]?"([^"]*)"/i', $string, $matches);

	$return = array();
	if (!empty($matches)) {
		$c_matches = count($matches[1]);
		for ($i = 0; $i < $c_matches; ++$i)
			$return[$matches[1][$i]] = $matches[2][$i];
	}

	return $return;
}
/**
 * Generiert das Inhaltsverzeichnis
 *
 * @param string|array $pages 
 */
function generateTOC(array $pages, $path)
{
	if (!empty($pages)) {
		global $lang, $tpl, $uri;

		$toc = array();
		$i = 0;
		foreach ($pages as $page) {
			$attributes = getHtmlAttributes($page);
			$page_num = $i + 1;
			$toc[$i]['title'] = !empty($attributes['title']) ? $attributes['title'] : sprintf($lang->t('static_pages', 'page'), $page_num);
			$toc[$i]['uri'] = $uri->route($path, 1) . 'page_' . $page_num . '/';
			$toc[$i]['selected'] = (validate::isNumber($uri->page) === false && $i === 0) || $uri->page === $page_num ? true : false;
			++$i;
		}
		$tpl->assign('toc', $toc);
		return view::fetchTemplate('static_pages/toc.tpl');
	}
	return '';
}