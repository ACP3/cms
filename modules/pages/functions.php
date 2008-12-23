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

	$pages = $db->query('SELECT n.id, n.start, n.end, n.mode, n.block_id, n.left_id, n.right_id, n.title, n.uri, n.target, COUNT(*)-1 AS level, ROUND((n.right_id - n.left_id - 1) / 2) AS children FROM ' . CONFIG_DB_PRE . 'pages AS n, ' . CONFIG_DB_PRE . 'pages AS p WHERE n.left_id BETWEEN p.left_id AND p.right_id GROUP BY n.left_id ORDER BY n.left_id;');
	$c_pages = count($pages);

	if ($c_pages > 0) {
		$blocks = $db->select('id, title, index_name', 'pages_blocks');
		$c_blocks = count($blocks);

		for ($i = 0; $i < $c_pages; ++$i) {
			for ($j = 0; $j < $c_blocks; ++$j) {
				if ($pages[$i]['block_id'] == $blocks[$j]['id']) {
					$pages[$i]['block_title'] = $blocks[$j]['title'];
					$pages[$i]['block_name'] = $blocks[$j]['index_name'];
				} elseif (empty($pages[$i]['block_id'])) {
					$pages[$i]['block_title'] = '';
					$pages[$i]['block_name'] = '';
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

	$bool = $db->delete('pages', 'left_id = \'' . $left_id . '\'');
	$bool2 = $db->query('UPDATE ' . CONFIG_DB_PRE . 'pages SET left_id = left_id - 1, right_id = right_id - 1 WHERE left_id BETWEEN ' . $left_id . ' AND ' . $right_id, 0);
	$bool3 = $db->query('UPDATE ' . CONFIG_DB_PRE . 'pages SET left_id = left_id - 2 WHERE left_id > ' . $right_id, 0);
	$bool4 = $db->query('UPDATE ' . CONFIG_DB_PRE . 'pages SET right_id = right_id - 2 WHERE right_id > ' . $right_id, 0);

	// Cache löschen
	cache::delete('pages_list_id_' . $id);

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

	if (!validate::isNumber($parent) || $db->select('id', 'pages', 'id = \'' . $parent . '\'', 0, 0, 0, 1) == 0) {
		$node = $db->select('right_id', 'pages', 0, 'right_id DESC', 1);

		$insert_values['left_id'] = !empty($node) ? $node[0]['right_id'] + 1 : 1;
		$insert_values['right_id'] = !empty($node) ? $node[0]['right_id'] + 2 : 2;

		$db->insert('pages', $insert_values);
		$root = $db->select('LAST_INSERT_ID() AS root', 'pages');

		return $db->update('pages', array('root_id' => $root[0]['root']), 'id = \'' . $root[0]['root'] . '\'');
	} else {
		$node = $db->select('root_id, right_id', 'pages', 'id = \'' . $parent . '\'');
		$db->query('UPDATE ' . CONFIG_DB_PRE . 'pages SET right_id = right_id + 2 WHERE right_id >= ' . $node[0]['right_id'], 0);
		$db->query('UPDATE ' . CONFIG_DB_PRE . 'pages SET left_id = left_id + 2 WHERE left_id > ' . $node[0]['right_id'], 0);

		$insert_values['root_id'] = $node[0]['root_id'];
		$insert_values['left_id'] = $node[0]['right_id'];
		$insert_values['right_id'] = $node[0]['right_id'] + 1;

		return $db->insert('pages', $insert_values);
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
				// Titel für den aktuellen Block setzen
				if (!empty($row['block_title'])) {
					$block = $row['block_title'];
				} elseif ($row['level'] == '0' && empty($row['block_title'])) {
					$block = $lang->t('pages', 'do_not_display');
				}
				$row['selected'] = selectEntry('parent', $row['id'], $parent);
				$row['spaces'] = str_repeat('&nbsp;&nbsp;', $row['level']);
				$output[$block][$i] = $row;
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
					$href = uri('pages/list/item_' . $pages[$i]['id']);
					$target = ($pages[$i]['mode'] == 2 || $pages[$i]['mode'] == 3) && $pages[$i]['target'] == 2 ? ' onclick="window.open(this.href); return false"' : '';
					$link = '<a href="' . $href . '" class="' . $css . '"' . $target . '>' . $pages[$i]['title'] . '</a>';
					$indent = str_repeat("\t\t", $pages[$i]['level']);

					// Falls für Knoten Kindelemente vorhanden sind, neue Unterliste erstellen
					if (isset($pages[$i + 1]) && !empty($pages[$i + 1]['block_name']) && $pages[$i + 1]['level'] > $pages[$i]['level']) {
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