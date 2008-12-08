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
	if (!cache::check('pages_list_id_' . $id))
		setPagesCache($id);

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
		for ($i = 0; $i < $c_pages; ++$i) {
			// Bestimmen, ob die Seite die Erste und/oder Letzte eines Blocks/Knotens ist
			$first = $last = false;
			if ($i == 0 || !empty($pages[$i - 1]) && $pages[$i - 1]['block_title'] != $pages[$i]['block_title'])
				$first = true;
			if ($i == $c_pages - 1 || isset($pages[$i + 1]) && $pages[$i]['children'] == '0' && $pages[$i]['block_title'] != $pages[$i + 1]['block_title'])
				$last = true;

			$pages[$i]['first'] = $first;
			$pages[$i]['last'] = $last;
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
	//if (!cache::check('pages'))
		setNavbarCache();

	return cache::output('pages');
}
/**
 * Auflistung der Seiten
 *
 * @param integer $mode
 * 	1 = Seiten für das Admin Panel auflisten
 * 	2 = Übergeordnete Seiten anzeigen
 * @param integer $parent
 *  ID des Elternknotens
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
		global $lang;

		for ($i = 0; $i < $c_pages; ++$i) {
			// Titel für den aktuellen Block setzen
			if (!empty($pages[$i]['block_title'])) {
				$block = $pages[$i]['block_title'];
			} elseif ($pages[$i]['level'] == '0' && empty($pages[$i]['block_title'])) {
				$block = $lang->t('pages', 'do_not_display');
			}
			$pages[$i]['selected'] = selectEntry('parent', $pages[$i]['id'], $parent);
			$pages[$i]['spaces'] = str_repeat('&nbsp;', $pages[$i]['level']);
			$output[$block][$i] = $pages[$i];
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
 * @return string
 */
function processNavbar($block) {
	static $navbar = array();

	// Navigationsleiste sofort ausgeben, falls diese schon einmal verarbeitet wurde...
	if (isset($navbar[$block])) {
		return $navbar[$block];
	// ...ansonsten Verarbeitung starten
	} else {
		// Gecachete Navigationsleiste einbinden
		$pages = getNavbarCache();
		$c_pages = count($pages);

		if ($c_pages > 0) {
			global $date, $uri;

			$navbar[$block] = "<ul class=\"navigation-" . $block . "\">\n";

			// Aktuellen Zeitstempel holen
			$time = $date->timestamp();

			for ($i = 0; $i < $c_pages; ++$i) {
				// Checken, ob die Seite im angeforderten Block liegt und ob diese veröffentlicht ist
				if ($pages[$i]['block_name'] == $block && $pages[$i]['start'] == $pages[$i]['end'] && $pages[$i]['start'] <= $time || $pages[$i]['start'] != $pages[$i]['end'] && $pages[$i]['start'] <= $time && $pages[$i]['end'] >= $time) {
					$css = 'navi-' . $pages[$i]['id'] . ($uri->mod == 'pages' && $uri->page == 'list' && $uri->item == $pages[$i]['id'] || $uri->query == uri($pages[$i]['uri']) ? ' selected' : '');
					$href = uri('pages/list/item_' . $pages[$i]['id']);
					$target = ($pages[$i]['mode'] == 2 || $pages[$i]['mode'] == 3) && $pages[$i]['target'] == 2 ? ' onclick="window.open(this.href); return false"' : '';
					$link = '<a href="' . $href . '" class="' . $css . '"' . $target . '>' . $pages[$i]['title'] . '</a>';
					$indent = str_repeat("\t\t", $pages[$i]['level']);

					// Falls für Knoten Kindelemente vorhanden sind, neue Unterliste erstellen
					if (isset($pages[$i + 1]) && $pages[$i + 1]['level'] > $pages[$i]['level']) {
						$navbar[$block].= $indent . "\t<li>\n";
						$navbar[$block].= $indent . "\t\t" . $link . "\n";
						$navbar[$block].= $indent . "\t\t<ul>\n";
					// Elemente ohne Kindelemente
					} else {
						$navbar[$block].= $indent . "\t<li>" . $link . "</li>\n";
						// Liste für untergeordnete Elemente schließen
						if (isset($pages[$i + 1]) && $pages[$i + 1]['level'] < $pages[$i]['level'] || !isset($pages[$i + 1]) && $pages[$i]['level'] != '0') {
							// Differenz ermitteln, wieviele Level zwischen dem aktuellen und dem nachfolgendem Element liegen
							$diff = (isset($pages[$i + 1]['level']) ? $pages[$i]['level'] - $pages[$i + 1]['level'] : $pages[$i]['level']) * 2;
							for ($diff; $diff > 0; --$diff) {
								$navbar[$block].= str_repeat("\t", $diff) . ($diff % 2 == 0 ? '</ul>' : '</li>') . "\n";
							}
						}
					}
				}
			}
			$navbar[$block].= "</ul>";
			return $navbar[$block];
		}
		return '';
	}
}
?>