<?php
/**
 * Pages
 *
 * @author Goratsch Webdesign
 * @package ACP3
 * @subpackage Modules
 */
/**
 * Auflistung der übergeordneten Seiten
 *
 * @param integer $id
 * @param integer $parent
 * @return array
 */
function pagesList($id = 0, $parent = 0, $newVar = 0)
{
	global $db;
	static $output = array(), $key = 0, $spaces = '';

	$pages = $db->select('id, title', 'pages', '(mode = \'1\' OR mode = \'2\') AND parent = \'' . $id . '\' AND id != \'' . $newVar . '\'', 'block_id ASC, sort ASC, title ASC');
	$c_pages = count($pages);

	if ($c_pages > 0) {
		if ($id != 0)
			$spaces.= '&nbsp;&nbsp;';

		for ($i = 0; $i < $c_pages; ++$i) {
			$output[$key]['id'] = $pages[$i]['id'];
			$output[$key]['selected'] = selectEntry('parent', $pages[$i]['id'], $parent);
			$output[$key]['title'] = $spaces . $pages[$i]['title'];
			$key++;

			pagesList($pages[$i]['id'], $parent, $newVar);

			if ($i == $c_pages - 1) {
				$spaces = substr($spaces, 0, -12);
			}
		}
	}
	return $output;
}
/**
 * Überprüfung der übergeordneten Seite, damit keine Endlosschleifen entstehen
 *
 * @param integer $id
 * @param integer $parent_id
 * @return boolean
 */
function parentCheck($id, $parent_id)
{
	global $db;

	$parents = $db->select('parent', 'pages', 'id = \'' . $parent_id . '\'');
	$c_parents = count($parents);

	if ($c_parents > 0) {
		for ($i = 0; $i < $c_parents; ++$i) {
			if ($parents[$i]['parent'] == $id)
				return true;

			if ($db->select('id', 'pages', 'id = \'' . $parents[$i]['parent'] . '\'', 0, 0, 0, 1) > 0)
				parentCheck($id, $parents[$i]['parent']);
		}
	}
	return false;
}
/**
 * Verarbeitet die Navigationsleiste und selektiert die aktuelle Seite,
 * falls diese sich ebenfalls in der Navigationsleiste befindet
 *
 * @return mixed
 */
function processNavbar()
{
	global $db, $modules;

	if (!cache::check('pages')) {
		cache::create('pages', $db->select('p.id, p.start, p.end, p.mode, p.title, p.target, b.index_name AS block_name', 'pages AS p, ' . CONFIG_DB_PRE . 'pages_blocks AS b', 'p.block_id != \'0\' AND p.block_id = b.id', 'p.sort ASC, p.title ASC'));
	}
	$pages = cache::output('pages');
	$c_pages = count($pages);

	if ($c_pages > 0) {
		$navbar = array();
		$selected = ' selected';

		for ($i = 0; $i < $c_pages; ++$i) {
			if ($pages[$i]['start'] == $pages[$i]['end']  && $pages[$i]['start'] <= dateAligned(2, time()) || $pages[$i]['start'] != $pages[$i]['end'] && $pages[$i]['start'] <= dateAligned(2, time()) && $pages[$i]['end'] >= dateAligned(2, time())) {
				$link['css'] = 'navi-' . $pages[$i]['id'] . ($modules->mod == 'pages' && $modules->page == 'list' && $modules->id == $pages[$i]['id'] ? $selected : '');
				$link['href'] = uri('pages/list/item_' . $pages[$i]['id']);
				$link['target'] = ($pages[$i]['mode'] == 2 || $pages[$i]['mode'] == 3) && $pages[$i]['target'] == 2 ? ' onclick="window.open(this.href); return false"' : '';
				$link['title'] = $pages[$i]['title'];

				$navbar[$pages[$i]['block_name']][$i] = $link;
			}
		}
		return $navbar;
	}
	return null;
}
?>