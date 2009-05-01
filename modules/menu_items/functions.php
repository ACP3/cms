<?php
/**
 * Pages
 *
 * @author Goratsch Webdesign
 * @package ACP3
 * @subpackage Modules
 */
/**
 * Erstellt den Cache für die Menüpunkte
 *
 * @return boolean
 */
function setMenuItemsCache() {
	global $date, $db, $lang;

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

		$mode_replace = array($lang->t('menu_items', 'module'), $lang->t('menu_items', 'dynamic_page'), $lang->t('menu_items', 'hyperlink'));

		for ($i = 0; $i < $c_pages; ++$i) {
			$pages[$i]['period'] = $date->period($pages[$i]['start'], $pages[$i]['end']);
			$pages[$i]['mode_formated'] = str_replace(array('1', '2', '3'), $mode_replace, $pages[$i]['mode']);

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
				$j = $i + 1;
				for ($j = $i + 1; $j < $c_pages; ++$j) {
					if ($pages[$i]['level'] == $pages[$j]['level'] && $pages[$i]['block_name'] == $pages[$j]['block_name']) {
						$found = true;
						break;
					}
				}
				if (!isset($found))
					$last = true;
				else
					unset($found);
			}

			$pages[$i]['first'] = $first;
			$pages[$i]['last'] = $last;
		}
	}
	return cache::create('menu_items', $pages);
}
/**
 * Bindet die gecacheten Menüpunkte ein
 *
 * @return array
 */
function getMenuItemsCache()
{
	if (!cache::check('menu_items'))
		setMenuItemsCache();

	return cache::output('menu_items');
}
/**
 * Löscht einen Knoten und verschiebt seine Kinder eine Ebene nach oben
 *
 * @param integer $id
 *  Die ID des zu löschenden Datensatzes
 *
 * @return boolean
 */
function deleteNode($id)
{
	if (!empty($id) && validate::isNumber($id)) {
		global $db;

		$lr = $db->select('left_id, right_id', 'menu_items', 'id = \'' . $id . '\'');
		if (count($lr) == 1) {
			$db->link->beginTransaction();

			$bool = $db->delete('menu_items', 'left_id = \'' . $lr[0]['left_id'] . '\'');
			$bool2 = $db->query('UPDATE ' . CONFIG_DB_PRE . 'menu_items SET left_id = left_id - 1, right_id = right_id - 1 WHERE left_id BETWEEN ' . $lr[0]['left_id'] . ' AND ' . $lr[0]['right_id'], 0);
			$bool3 = $db->query('UPDATE ' . CONFIG_DB_PRE . 'menu_items SET left_id = left_id - 2 WHERE left_id > ' . $lr[0]['right_id'], 0);
			$bool4 = $db->query('UPDATE ' . CONFIG_DB_PRE . 'menu_items SET right_id = right_id - 2 WHERE right_id > ' . $lr[0]['right_id'], 0);

			$db->link->commit();

			return $bool !== null && $bool2 !== null && $bool3 !== null && $bool4 !== null ? true : false;
		}
	}
	return false;
}
/**
 * Erstellt einen neuen Knoten
 *
 * @param integer $parent
 * @param array $insert_values
 *
 * @return boolean
 */
function insertNode($parent, $insert_values)
{
	global $db;

	if (!validate::isNumber($parent) || $db->countRows('*', 'menu_items', 'id = \'' . $parent . '\'') == 0) {
		$node = $db->select('right_id', 'menu_items', 'block_id = \'' . $db->escape($insert_values['block_id']) . '\'', 'right_id DESC', 1);
		if (empty($node)) {
			$node = $db->select('right_id', 'menu_items', 'block_id < \'' . $db->escape($insert_values['block_id']) . '\'', 'block_id DESC, right_id DESC', 1);
		}
		$insert_values['left_id'] = !empty($node) ? $node[0]['right_id'] + 1 : 1;
		$insert_values['right_id'] = !empty($node) ? $node[0]['right_id'] + 2 : 2;

		$bool = $db->insert('menu_items', $insert_values);
		$root = $db->select('LAST_INSERT_ID() AS root_id', 'menu_items');

		$bool2 = $db->update('menu_items', array('root_id' => $root[0]['root_id']), 'id = \'' . $root[0]['root_id'] . '\'');
		$bool3 = $db->query('UPDATE ' . CONFIG_DB_PRE . 'menu_items SET left_id = left_id + 2, right_id = right_id + 2 WHERE block_id > ' . $db->escape($insert_values['block_id']), 0);

		return $bool !== null && $bool2 !== null && $bool3 !== null ? true : false;
	} else {
		$node = $db->select('root_id, right_id', 'menu_items', 'id = \'' . $parent . '\'');

		$db->link->beginTransaction();

		$db->query('UPDATE ' . CONFIG_DB_PRE . 'menu_items SET left_id = left_id + 2, right_id = right_id + 2 WHERE left_id > ' . $node[0]['right_id'], 0);
		$db->query('UPDATE ' . CONFIG_DB_PRE . 'menu_items SET right_id = right_id + 2 WHERE right_id = ' . $node[0]['right_id'], 0);

		$db->link->commit();

		$insert_values['root_id'] = $node[0]['root_id'];
		$insert_values['left_id'] = $node[0]['right_id'];
		$insert_values['right_id'] = $node[0]['right_id'] + 1;

		return $db->insert('menu_items', $insert_values);
	}
}
/**
 * Auflistung der Seiten
 *
 * @param integer $parent
 *  ID des Elternknotens
 * @return array
 */
function pagesList($parent = 0, $left = 0, $right = 0) {
	static $pages = array();

	// Menüpunkte einbinden
	if (empty($pages))
		$pages = getMenuItemsCache();

	$output = array();

	if (count($pages) > 0) {
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
 * @param string $block
 *	Name des Blocks, für welchen die Navigationspunkte ausgegeben werden sollen
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
		global $date, $db, $uri;

		$pages = getMenuItemsCache();
		$c_pages = count($pages);
		$hide_until = 0;
		$visible_pages = array();
		$j = 0;

		// Aktuellen Zeitstempel holen
		$time = $date->timestamp();

		for ($i = 0; $i < $c_pages; ++$i) {
			if ($pages[$i]['block_name'] == $block) {
				if ($pages[$i]['display'] == 0 && $pages[$i]['right_id'] > $hide_until) {
					$hide_until = $pages[$i]['right_id'];
				}
				// Checken, ob die Seite im angeforderten Block liegt und ob diese veröffentlicht ist
				if ($pages[$i]['display'] == 1 && $pages[$i]['right_id'] > $hide_until &&
					$pages[$i]['start'] == $pages[$i]['end'] && $pages[$i]['start'] <= $time ||
					$pages[$i]['start'] != $pages[$i]['end'] && $pages[$i]['start'] <= $time && $pages[$i]['end'] >= $time) {
					$visible_pages[$j] = $pages[$i];
					$j++;
				}
			}
		}
		$pages = $visible_pages;
		$c_pages = count($pages);

		if ($c_pages > 0) {
			if (uri($uri->query) != uri($uri->mod) && $db->query('SELECT COUNT(*) FROM ' . CONFIG_DB_PRE . 'menu_items AS m JOIN ' . CONFIG_DB_PRE . 'menu_items_blocks AS b ON(m.block_id = b.id) WHERE b.index_name = \'' . $block . '\' AND m.uri = \'' . $uri->query . '\'', 1) > 0) {
				$link = $uri->query;
			} else {
				$link = $uri->mod;
			}
			$select = $db->query('SELECT m.left_id FROM ' . CONFIG_DB_PRE . 'menu_items AS m JOIN ' . CONFIG_DB_PRE . 'menu_items_blocks AS b ON(m.block_id = b.id) WHERE b.index_name = \'' . $block . '\' AND m.uri = \'' . $link . '\'');

			$navbar[$block] = '';

			for ($i = 0; $i < $c_pages; ++$i) {
				$css = 'navi-' . $pages[$i]['id'];
				// Menüpunkt selektieren
				if (!empty($select) && defined('IN_ACP3') && $pages[$i]['left_id'] <= $select[0]['left_id'] && $pages[$i]['right_id'] > $select[0]['left_id']) {
					$css.= ' selected';
				}
				$href = $pages[$i]['mode'] == '1' || $pages[$i]['mode'] == '2' ? uri($pages[$i]['uri']) : $pages[$i]['uri'];
				$target = $pages[$i]['target'] == 2 ? ' onclick="window.open(this.href); return false"' : '';
				$link = '<a href="' . $href . '" class="' . $css . '"' . $target . '>' . $pages[$i]['title'] . '</a>';
				$indent = str_repeat("\t\t", $pages[$i]['level']);

				// Falls für Knoten Kindelemente vorhanden sind, neue Unterliste erstellen
				if (isset($pages[$i + 1]) && $pages[$i + 1]['level'] > $pages[$i]['level']) {
					$navbar[$block].= $indent . "\t<li>\n";
					$navbar[$block].= $indent . "\t\t" . $link . "\n";
					$navbar[$block].= $indent . "\t\t<ul class=\"navigation-" . $block . '-subnav-' . $pages[$i]['id'] . "\">\n";
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
			$navbar[$block] = !empty($navbar[$block]) ? "<ul class=\"navigation-" . $block . "\">\n" . $navbar[$block] . '</ul>' : '';
			return $navbar[$block];
		}
		return '';
	}
}
?>