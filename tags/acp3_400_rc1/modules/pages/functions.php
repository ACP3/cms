<?php
/**
 * Pages
 *
 * @author Goratsch Webdesign
 * @package ACP3
 * @subpackage Modules
 */
function generatePagesCache() {
	global $db;

	$pages = $db->query('SELECT p.id, p.start, p.end, p.mode, p.parent, p.block_id, p.sort, p.title, p.uri, p.target, b.title AS block_title, b.index_name AS block_name FROM ' . CONFIG_DB_PRE . 'pages AS p LEFT JOIN ' . CONFIG_DB_PRE . 'pages_blocks AS b ON (p.block_id = b.id) ORDER BY p.block_id ASC, p.sort ASC, p.title ASC');
	$c_pages = count($pages);
	$items = array();

	if ($c_pages > 0) {
		for ($i = 0; $i < $c_pages; ++$i) {
			foreach ($pages[$i] as $key => $value) {
				$items[$pages[$i]['id']][$key] = $value;
			}
			$items[$pages[$i]['parent']]['children'][$pages[$i]['id']] = & $items[$pages[$i]['id']];
		}
	}
	cache::create('pages', !empty($items[0]['children']) ? $items[0]['children'] : array());
}
/**
 * Auflistung der Seiten
 *
 * @param integer $mode
 * 	1 = Seiten für das Admin Panel auflisten
 * 	2 = Übergeordnete Seiten anzeigen
 * @param array $pages
 * @param integer $parent
 * @param integer $self
 * @return array
 */
function pagesList($mode = 1, $pages = 0, $parent = 0, $self = 0, $indent = 0) {
	static $output = array(), $last_block = '', $key = 0;

	if (empty($pages)) {
		// Der Baum ist schon vorhanden
		if (!empty($output)) {
			return $output;
		}
		if (!cache::check('pages')) {
			generatePagesCache();
		}
		$pages = cache::output('pages');
	}

	if (count($pages) > 0) {
		global $lang;
		foreach ($pages as $row) {
			if (!empty($row['block_title']))
				$last_block = $row['block_title'];
			elseif (empty($row['parent']) && empty($row['block_title']))
				$last_block = $lang->t('pages', 'do_not_display');

			if ($mode == 2 && $self != $row['id']) {
				$output[$last_block][$key] = array(
					'id' => $row['id'],
					'start' => $row['start'],
					'end' => $row['end'],
					'mode' => $row['mode'],
					'block_id' => $row['block_id'],
					'title' => $row['title'],
					'selected' => selectEntry('parent', $row['id'], $parent),
					'spaces' => str_repeat('&nbsp;', $indent),
				);
				$key++;
				if (!empty($row['children'])) {
					pagesList($mode, $row['children'], $parent, $self, $indent + 2);
				}
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
 * @param integer $block
 * @return boolean
 */
function parentCheck($id, $parent_id, $block) {
	global $db;

	$parents = $db->select('parent, block_id', 'pages', 'id = \'' . $parent_id . '\'');
	$c_parents = count($parents);

	if ($c_parents > 0) {
		for ($i = 0; $i < $c_parents; ++$i) {
			if ($parents[$i]['parent'] == $id || $block != '0' && $parents[$i]['block_id'] == '0')
				return false;

			if ($db->select('id', 'pages', 'id = \'' . $parents[$i]['parent'] . '\'', 0, 0, 0, 1) > 0)
				parentCheck($id, $parents[$i]['parent'], $block);
		}
	}
	return true;
}
/**
 * Verarbeitet die Navigationsleiste und selektiert die aktuelle Seite,
 * falls diese sich ebenfalls in der Navigationsleiste befindet
 *
 * @return mixed
 */
function processNavbar($block, $pages = 0) {
	static $navbar = array();

	// Navigationsleiste sofort ausgeben, falls diese schon einmal verarbeitet wurde
	if (empty($pages) && isset($navbar[$block])) {
		return $navbar[$block];
	} else {
		if (empty($pages)) {
			if (!cache::check('pages')) {
				generatePagesCache();
			}
			$pages = cache::output('pages');
		}
		$c_pages = count($pages);

		if ($c_pages > 0) {
			global $date, $uri;
			static $tabs = '';

			if (!empty($navbar[$block]))
				$tabs .= "\t\t";

			$i = 0;
			if (empty($navbar[$block])) {
				$navbar[$block] = "<ul class=\"navigation-" . $block . "\">\n";
			} else {
				$navbar[$block] .= $tabs . "<ul>\n";
			}
			foreach ($pages as $row) {
				if ($row['block_name'] == $block && !empty($row['block_id']) && $row['start'] == $row['end'] && $row['start'] <= $date->timestamp() || $row['start'] != $row['end'] && $row['start'] <= $date->timestamp() && $row['end'] >= $date->timestamp()) {
					$css = 'navi-' . $row['id'] . ($uri->mod == 'pages' && $uri->page == 'list' && $uri->item == $row['id'] || $uri->query == uri($row['uri']) ? ' selected' : '');
					$href = uri('pages/list/item_' . $row['id']);
					$target = ($row['mode'] == 2 || $row['mode'] == 3) && $row['target'] == 2 ? ' onclick="window.open(this.href); return false"' : '';
					$link = '<a href="' . $href . '" class="' . $css . '"' . $target . '>' . $row['title'] . '</a>';
					if (empty($row['children'])) {
						$navbar[$block] .= $tabs . "\t<li>" . $link . "</li>\n";
					} else {
						$navbar[$block] .= $tabs . "\t<li>\n" . $tabs . "\t\t" . $link . "\n";
						processNavbar($block, $row['children']);
						$navbar[$block] .= $tabs . "\t</li>\n";
					}
				}
				// Navigationsleiste einrücken
				if ($i == $c_pages - 1) {
					// Mögliche HTML Fehler beheben
					$search = array($tabs . "<ul>\n" . $tabs . "</ul>\n", "<ul class=\"navigation-" . $block . "\">\n</ul>\n");
					$navbar[$block] .= $tabs . "</ul>\n";
					$navbar[$block] = str_replace($search, '', $navbar[$block]);
					$tabs = substr($tabs, 0, -2);
				}
				++$i;
			}
			return $navbar[$block];
		}
		return '';
	}
}
?>