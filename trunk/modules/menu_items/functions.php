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
	return cache::create('menu_items_list_id_' . $id, $db->select('mode, urit', 'menu_items', 'id = \'' . $id . '\''));
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
	if (!cache::check('menu_items_list_id_' . $id))
		setPagesCache($id);

	return cache::output('menu_items_list_id_' . $id);
}
/**
 * Erstellt den Cache für die Navigationsleisten
 *
 * @return boolean
 */
function setNavbarCache() {
	global $db;

	$pages = $db->query('SELECT n.*, COUNT(*)-1 AS level, ROUND((n.right_id - n.left_id - 1) / 2) AS children FROM ' . CONFIG_DB_PRE . 'menu_items AS n, ' . CONFIG_DB_PRE . 'menu_items AS p WHERE n.left_id BETWEEN p.left_id AND p.right_id GROUP BY n.left_id ORDER BY n.left_id');
	$c_pages = count($pages);

	if ($c_pages > 0) {
		$blocks = $db->select('id, title, index_name', 'menu_items_blocks');
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
			if ($i == 0 ||
				isset($pages[$i - 1]) &&
				($pages[$i - 1]['level'] < $pages[$i]['level'] ||
				$pages[$i]['level'] < $pages[$i - 1]['level'] && $pages[$i]['block_name'] != $pages[$i - 1]['block_name'] ||
				$pages[$i]['level'] == $pages[$i - 1]['level'] && $pages[$i]['block_name'] != $pages[$i - 1]['block_name']))
				$first = true;
			if ($i == $c_pages - 1 ||
				isset($pages[$i + 1]) &&
				($pages[$i]['level'] == 0 && $pages[$i + 1]['level'] == 0 && $pages[$i]['block_name'] != $pages[$i + 1]['block_name'] ||
				$pages[$i]['level'] > $pages[$i + 1]['level']))
				$last = true;

			// Checken, ob für das aktuelle Element noch Nachfolger existieren
			if (!$last) {
				$last = true;
				$found = false;
				$j = $i + 1;
				for ($j = $i + 1; $j < $c_pages; ++$j) {
					if ($pages[$i]['level'] == $pages[$j]['level'] && $pages[$i]['block_name'] == $pages[$j]['block_name'])
						$found = true;
				}
				if ($found)
					$last = false;
				$found = false;
			}

			$pages[$i]['first'] = $first;
			$pages[$i]['last'] = $last;
		}
	}
	return cache::create('menu_items', $pages);
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

	return cache::output('menu_items');
}
/**
 * Löscht einen Knoten und aktualisiert die linken und rechten Werte
 *
 * @param integer $left_id
 *  Der linke Wert des zu löschenden Datensatzes
 * @param integer $right_id
 *  Der rechte Wert des zu löschenden Datensatzes
 * @return boolean
 */
function deleteNode($id, $left_id, $right_id)
{
	global $db;

	$bool = $db->delete('menu_items', 'left_id = \'' . $left_id . '\'');
	$bool2 = $db->query('UPDATE ' . CONFIG_DB_PRE . 'menu_items SET left_id = left_id - 1, right_id = right_id - 1 WHERE left_id BETWEEN ' . $left_id . ' AND ' . $right_id, 0);
	$bool3 = $db->query('UPDATE ' . CONFIG_DB_PRE . 'menu_items SET left_id = left_id - 2 WHERE left_id > ' . $right_id, 0);
	$bool4 = $db->query('UPDATE ' . CONFIG_DB_PRE . 'menu_items SET right_id = right_id - 2 WHERE right_id > ' . $right_id, 0);

	// Cache löschen
	cache::delete('menu_items_list_id_' . $id);

	return $bool && $bool2 && $bool3 && $bool4 ? true : false;
}
/**
 * Erstellt einen neuen Knoten
 * @param integer $parent
 * @param array $insert_values
 * @return boolean
 */
function insertNode($parent, $insert_values)
{
	global $db;

	if (!validate::isNumber($parent) || $db->select('id', 'menu_items', 'id = \'' . $parent . '\'', 0, 0, 0, 1) == 0) {
		$node = $db->select('right_id', 'menu_items', 0, 'right_id DESC', 1);

		$insert_values['left_id'] = !empty($node) ? $node[0]['right_id'] + 1 : 1;
		$insert_values['right_id'] = !empty($node) ? $node[0]['right_id'] + 2 : 2;

		$db->insert('menu_items', $insert_values);
		$root = $db->select('LAST_INSERT_ID() AS root', 'menu_items');

		return $db->update('menu_items', array('root_id' => $root[0]['root']), 'id = \'' . $root[0]['root'] . '\'');
	} else {
		$node = $db->select('root_id, right_id', 'menu_items', 'id = \'' . $parent . '\'');
		$db->query('UPDATE ' . CONFIG_DB_PRE . 'menu_items SET right_id = right_id + 2 WHERE right_id >= ' . $node[0]['right_id'], 0);
		$db->query('UPDATE ' . CONFIG_DB_PRE . 'menu_items SET left_id = left_id + 2 WHERE left_id > ' . $node[0]['right_id'], 0);

		$insert_values['root_id'] = $node[0]['root_id'];
		$insert_values['left_id'] = $node[0]['right_id'];
		$insert_values['right_id'] = $node[0]['right_id'] + 1;

		return $db->insert('menu_items', $insert_values);
	}
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
function pagesList($parent = 0, $left = 0, $right = 0) {
	static $pages = array();

	// Menüpunkte einbinden
	if (empty($pages))
		$pages = getNavbarCache();

	$output = array();

	if (count($pages) > 0) {
		global $lang;

		$i = 0;
		foreach($pages as $row) {
			if (!($row['left_id'] >= $left && $row['right_id'] <= $right)) {
				$row['selected'] = selectEntry('parent', $row['id'], $parent);
				$row['spaces'] = str_repeat('&nbsp;&nbsp;', $row['level']);

				// Titel für den aktuellen Block setzen
				$output[$row['block_title']][$i] = $row;
				$i++;
			}
		}
	}
	return $output;
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

			$navbar[$block] = '';

			// Aktuellen Zeitstempel holen
			$time = $date->timestamp();

			for ($i = 0; $i < $c_pages; ++$i) {
				// Checken, ob die Seite im angeforderten Block liegt und ob diese veröffentlicht ist
				if ($pages[$i]['block_name'] == $block && $pages[$i]['start'] == $pages[$i]['end'] && $pages[$i]['start'] <= $time || $pages[$i]['start'] != $pages[$i]['end'] && $pages[$i]['start'] <= $time && $pages[$i]['end'] >= $time) {
					$css = 'navi-' . $pages[$i]['id'] . ($uri->mod == 'pages' && $uri->page == 'list' && $uri->item == $pages[$i]['id'] || $uri->query == uri($pages[$i]['uri']) ? ' selected' : '');
					$href = uri('menu_items/list/item_' . $pages[$i]['id']);
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
			$navbar[$block] = !empty($navbar[$block]) ? "<ul class=\"navigation-" . $block . "\">\n" . $navbar[$block] . '</ul>' : '';
			return $navbar[$block];
		}
		return '';
	}
}
?>