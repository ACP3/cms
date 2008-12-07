<?php
/**
 * Pages
 *
 * @author Goratsch Webdesign
 * @package ACP3
 * @subpackage Modules
 */
/**
 * Erstellt den Cache für einen Menüpunkt
 *
 * @param integer $id
 *  Die ID des Menüpunktes
 * @return boolean
 */
function setPagesCache($id)
{
	global $db;
	return cache::create('pages_list_id_' . $id, $db->select('mode, uri, text', 'pages', 'id = \'' . $id . '\''));
}
/**
 * Bindet den Cache eines Menüpunktes anhand seiner ID ein
 *
 * @param integer $id
 *  Die ID des Menüpunktes
 * @return array
 */
function getPagesCache($id)
{
	if (!cache::check('pages_list_id_' . $id)) {
		setPagesCache($id);
	}
	return cache::output('pages_list_id_' . $id);
}
/**
 * Erstellt den Cache für die Navigationsleisten
 *
 * @return boolean
 */
function setNavbarCache() {
	global $db;

	$pages = $db->query('SELECT n.id, n.start, n.end, n.mode, n.block_id, n.title, n.uri, n.target, COUNT(*)-1 AS level, ROUND((n.right_id - n.left_id - 1) / 2) AS children FROM ' . CONFIG_DB_PRE . 'pages AS n, ' . CONFIG_DB_PRE . 'pages AS p WHERE n.left_id BETWEEN p.left_id AND p.right_id GROUP BY n.left_id ORDER BY n.left_id;');
	$c_pages = count($pages);

	if ($c_pages > 0) {
		$blocks = $db->select('id, title, index_name', 'pages_blocks');
		$c_blocks = count($blocks);

		for ($i = 0; $i < $c_pages; ++$i) {
			for ($j = 0; $j < $c_blocks; ++$j) {
				if ($pages[$i]['block_id'] == $blocks[$j]['id']) {
					$pages[$i]['block_title'] = $blocks[$j]['title'];
					$pages[$i]['block_name'] = $blocks[$j]['index_name'];
				}
			}
		}
	}
	return cache::create('pages', $pages);
}
/**
 * Bindet die cacheten Navigationsleisten ein
 *
 * @return array
 */
function getNavbarCache()
{
	if (!cache::check('pages'))
		setNavbarCache();

	return cache::output('pages');
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
function pagesList($mode = 1, $parent = 0) {
	static $pages = array();

	// Der Baum ist schon vorhanden
	if (!empty($pages)) {
		return $pages;
	}

	// Menüpunkte einbinden
	$pages = getNavbarCache();
	$c_pages = count($pages);

	if ($c_pages > 0) {
		$last_block = '';
		for ($i = 0; $i < $c_pages; ++$i) {
			$first = $last = false;
			if ($i == 0 || !empty($last_block) && $last_block != $pages[$i]['block_title'])
				$first = true;
			if ($i == $c_pages - 1)
				$last = true;

			// Titel für den aktuellen Block setzen
			if (!empty($pages[$i]['block_title'])) {
				$last_block = $pages[$i]['block_title'];
			} elseif ($pages[$i]['level'] == '0' && empty($pages[$i]['block_title'])) {
				global $lang;
				$last_block = $lang->t('pages', 'do_not_display');
			}
			$pages[$i]['first'] = $first;
			$pages[$i]['last'] = $last;
			$pages[$i]['selected'] = selectEntry('parent', $pages[$i]['id'], $parent);
			$pages[$i]['spaces'] = str_repeat('&nbsp;', $pages[$i]['level']);
			$output[$last_block][$i] = $pages[$i];
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
function processNavbar($block) {
	static $navbar = array();

	// Navigationsleiste sofort ausgeben, falls diese schon einmal verarbeitet wurde...
	if (isset($navbar[$block])) {
		return $navbar[$block];
	// ...ansonsten Verarbeitung starten
	} else {
		$pages = getNavbarCache();
		$c_pages = count($pages);

		if ($c_pages > 0) {
			global $date, $uri;

			$navbar[$block] = "<ul class=\"navigation-" . $block . "\">\n";

			// Aktuellen Zeitstempel holen
			$time = $date->timestamp();

			for ($i = 0; $i < $c_pages; ++$i) {
				if ($pages[$i]['block_name'] == $block && !empty($pages[$i]['block_id']) && $pages[$i]['start'] == $pages[$i]['end'] && $pages[$i]['start'] <= $time || $pages[$i]['start'] != $pages[$i]['end'] && $pages[$i]['start'] <= $time && $pages[$i]['end'] >= $time) {
					$css = 'navi-' . $pages[$i]['id'] . ($uri->mod == 'pages' && $uri->page == 'list' && $uri->item == $pages[$i]['id'] || $uri->query == uri($pages[$i]['uri']) ? ' selected' : '');
					$href = uri('pages/list/item_' . $pages[$i]['id']);
					$target = ($pages[$i]['mode'] == 2 || $pages[$i]['mode'] == 3) && $pages[$i]['target'] == 2 ? ' onclick="window.open(this.href); return false"' : '';
					$link = '<a href="' . $href . '" class="' . $css . '"' . $target . '>' . $pages[$i]['title'] . '</a>';

					$navbar[$block].= str_repeat("\t", $pages[$i]['level']) . '<li>' . $link . "</li>\n";
				}
			}
			$navbar[$block].= "</ul>\n";
			return $navbar[$block];
		}
		return '';
	}
}
?>