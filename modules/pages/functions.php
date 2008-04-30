<?php
/**
 * Pages
 *
 * @author Goratsch Webdesign
 * @package ACP3
 * @subpackage Modules
 */
function generatePagesCache()
{
	global $db;
	
	$pages = $db->select('p.id, p.start, p.end, p.mode, p.parent, p.block_id, p.sort, p.title, p.target, b.index_name AS block_name', 'pages AS p, ' . CONFIG_DB_PRE . 'pages_blocks AS b', 'p.block_id = b.id', 'p.block_id ASC, p.sort ASC, p.title ASC');
	$c_pages = count($pages);
	$items = array();
	
	if ($c_pages > 0) {
		
		for ($i = 0; $i < $c_pages; ++$i) {
			foreach ($pages[$i] as $key => $value) {
				$items[$pages[$i]['id']][$key] = $value;
			}
			$items[$pages[$i]['parent']]['children'][$pages[$i]['id']] =& $items[$pages[$i]['id']];
		}
	}
	cache::create('pages', $items[0]['children']);
}
/**
 * Auflistung der übergeordneten Seiten
 *
 * @param array $pages
 * @param integer $parent
 * @param integer $self
 * @return array
 */
function pagesList($pages = 0, $parent = 0, $self = 0)
{
	static $output = array(), $key = 0, $spaces = '';

	if (empty($pages)) {
		if (!cache::check('pages')) {
			generatePagesCache();
		}
		$pages = cache::output('pages');
	}
	$c_pages = count($pages);

	if ($c_pages > 0) {
		if ($key != 0)
			$spaces.= '&nbsp;&nbsp;';

		$i = 0;
		foreach ($pages as $row) {
			if ($self != $row['id']) {
				$output[$key]['id'] = $row['id'];
				$output[$key]['selected'] = selectEntry('parent', $row['id'], $parent);
				$output[$key]['title'] = $spaces . $row['title'];
				$key++;
				if (!empty($row['children'])) {
					pagesList($row['children'], $parent, $self);
				}
			}
			if ($i == $c_pages - 1) {
				$spaces = substr($spaces, 0, -12);
			}
			++$i;
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
function processNavbar($block, $pages = 0)
{
	if (empty($pages)) {
		if (!cache::check('pages')) {
			generatePagesCache();
		}
		$pages = cache::output('pages');
	}
	$c_pages = count($pages);

	if ($c_pages > 0) {
		global $modules;
		static $navbar = array();

		$i = 0;
		$navbar[$block].=  empty($navbar[$block]) ?  "<ul id=\"navigation-" . $block . "\">\n" : "\n<ul>\n";
		foreach ($pages as $row) {
			if ($row['block_name'] == $block && !empty($row['block_id']) && $row['start'] == $row['end']  && $row['start'] <= dateAligned(2, time()) || $row['start'] != $row['end'] && $row['start'] <= dateAligned(2, time()) && $row['end'] >= dateAligned(2, time())) {
				$css = 'navi-' . $row['id'] . ($modules->mod == 'pages' && $modules->page == 'list' && $modules->item == $row['id'] ? ' selected' : '');
				$href = uri('pages/list/item_' . $row['id']);
				$target = ($row['mode'] == 2 || $row['mode'] == 3) && $row['target'] == 2 ? ' onclick="window.open(this.href); return false"' : '';
				$navbar[$block].= "\t" . '<li><a href="' . $href . '" class="' . $css . '"' . $target . '>' . $row['title'] . '</a>';
				if (!empty($row['children'])) {
					processNavbar($block, $row['children']);
				}
				$navbar[$block].= "</li>\n";
			}
			if ($i == $c_pages - 1) {
				$navbar[$block].= "</ul>\n";
			}
			++$i;
		}
		return str_replace("\n<ul>\n</ul>\n", '', $navbar[$block]);
	}
	return '';
}
?>