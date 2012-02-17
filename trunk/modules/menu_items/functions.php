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
	global $db, $lang;

	$items = $db->query('SELECT n.*, COUNT(*)-1 AS level, ROUND((n.right_id - n.left_id - 1) / 2) AS children FROM {pre}menu_items AS p, {pre}menu_items AS n WHERE n.left_id BETWEEN p.left_id AND p.right_id GROUP BY n.left_id ORDER BY n.left_id');
	$c_items = count($items);

	if ($c_items > 0) {
		$blocks = $db->select('id, title, index_name', 'menu_items_blocks');
		$c_blocks = count($blocks);

		for ($i = 0; $i < $c_items; ++$i) {
			for ($j = 0; $j < $c_blocks; ++$j) {
				if ($items[$i]['block_id'] == $blocks[$j]['id']) {
					$items[$i]['block_title'] = $blocks[$j]['title'];
					$items[$i]['block_name'] = $blocks[$j]['index_name'];
				}
			}
		}

		$mode_search = array('1', '2', '3', '4');
		$mode_replace = array(
			$lang->t('menu_items', 'module'),
			$lang->t('menu_items', 'dynamic_page'),
			$lang->t('menu_items', 'hyperlink'),
			$lang->t('menu_items', 'static_page')
		);

		for ($i = 0; $i < $c_items; ++$i) {
			$items[$i]['mode_formated'] = str_replace($mode_search, $mode_replace, $items[$i]['mode']);

			// Bestimmen, ob die Seite die Erste und/oder Letzte eines Knotens ist
			$first = $last = true;
			if ($i > 0) {
				for ($j = $i - 1; $j >= 0; --$j) {
					if ($items[$j]['parent_id'] == $items[$i]['parent_id'] && $items[$j]['block_name'] == $items[$i]['block_name']) {
						$first = false;
						break;
					}
				}
			}

			for ($j = $i + 1; $j < $c_items; ++$j) {
				if ($items[$i]['parent_id'] == $items[$j]['parent_id'] && $items[$j]['block_name'] == $items[$i]['block_name']) {
					$last = false;
					break;
				}
			}

			$items[$i]['first'] = $first;
			$items[$i]['last'] = $last;
		}
	}
	return cache::create('menu_items', $items);
}
/**
 * Bindet die gecacheten Menüpunkte ein
 *
 * @return array
 */
function getMenuItemsCache()
{
	if (cache::check('menu_items') === false)
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
function menuItemsDeleteNode($id)
{
	if (!empty($id) && validate::isNumber($id) === true) {
		global $db;

		$lr = $db->select('left_id, right_id', 'menu_items', 'id = \'' . $id . '\'');
		if (count($lr) === 1) {
			$db->link->beginTransaction();

			$bool = $db->delete('menu_items', 'left_id = \'' . $lr[0]['left_id'] . '\'');
			$bool2 = $db->query('UPDATE {pre}menu_items SET left_id = left_id - 1, right_id = right_id - 1 WHERE left_id BETWEEN ' . $lr[0]['left_id'] . ' AND ' . $lr[0]['right_id'], 0);
			$bool3 = $db->query('UPDATE {pre}menu_items SET left_id = left_id - 2 WHERE left_id > ' . $lr[0]['right_id'], 0);
			$bool4 = $db->query('UPDATE {pre}menu_items SET right_id = right_id - 2 WHERE right_id > ' . $lr[0]['right_id'], 0);

			$db->link->commit();

			return $bool !== false && $bool2 !== false && $bool3 !== false && $bool4 !== false ? true : false;
		}
	}
	return false;
}
/**
 * Erstellt einen neuen Knoten
 *
 * @param integer $parent_id
 *	ID der übergeordneten Seite
 * @param array $insert_values
 *
 * @return boolean
 */
function menuItemsInsertNode($parent_id, array $insert_values)
{
	global $db;

	// Keine übergeordnete Seite zugewiesen
	if (validate::isNumber($parent_id) === false || $db->countRows('*', 'menu_items', 'id = \'' . $parent_id . '\'') == 0) {
		$db->link->beginTransaction();

		// Letzten Eintrag des zugewiesenen Blocks holen
		$node = $db->select('MAX(right_id) AS right_id', 'menu_items', 'block_id = \'' . $db->escape($insert_values['block_id']) . '\'');
		if (empty($node)) {
			$node = $db->select('MAX(right_id) AS right_id', 'menu_items', 'block_id < \'' . $db->escape($insert_values['block_id']) . '\'', 'block_id DESC');
		}

		// left_id und right_id Werte für das Anhängen entsprechend erhöhen
		$insert_values['left_id'] = !empty($node) ? $node[0]['right_id'] + 1 : 1;
		$insert_values['right_id'] = !empty($node) ? $node[0]['right_id'] + 2 : 2;

		$bool = $db->insert('menu_items', $insert_values);
		$root_id = $db->link->lastInsertId();

		$bool2 = $db->update('menu_items', array('root_id' => $root_id), 'id = \'' . $root_id . '\'');
		$bool3 = $db->query('UPDATE {pre}menu_items SET left_id = left_id + 2, right_id = right_id + 2 WHERE left_id > ' . $insert_values['left_id'], 0);

		$db->link->commit();

		return $bool !== null && $bool2 !== null && $bool3 !== null ? true : false;
	// Übergeordnete Seite zugewiesen
	} else {
		$parent = $db->select('root_id, left_id, right_id', 'menu_items', 'id = \'' . $parent_id . '\'');

		$db->link->beginTransaction();

		// Alle nachfolgenden Menüeinträge anpassen
		$db->query('UPDATE {pre}menu_items SET left_id = left_id + 2, right_id = right_id + 2 WHERE left_id > ' . $parent[0]['right_id'], 0);
		// Übergeordnete Menüpunkte anpassen
		$db->query('UPDATE {pre}menu_items SET right_id = right_id + 2 WHERE root_id = ' . $parent[0]['root_id'] . ' AND left_id <= ' . $parent[0]['left_id'] . ' AND right_id >= ' . $parent[0]['right_id'], 0);

		$db->link->commit();

		$insert_values['root_id'] = $parent[0]['root_id'];
		$insert_values['left_id'] = $parent[0]['right_id'];
		$insert_values['right_id'] = $parent[0]['right_id'] + 1;

		return $db->insert('menu_items', $insert_values);
	}
}
/**
 * Sorgt dafür, das ein Knoten in einen anderen Block verschoben werden kann
 *
 * @param integer $id
 *	ID des zu verschiebenden Knotens
 * @param integer $parent
 *	ID des neuen Elternelements
 * @param integer $block_id
 *	ID des neuen Blocks
 * @param array $update_values
 *
 * @return
 */
function menuItemsEditNode($id, $parent, $block_id, array $update_values)
{
	global $db;

	if (validate::isNumber($id) === true && (validate::isNumber($parent) === true || $parent == '') && validate::isNumber($block_id) === true) {
		// Die aktuelle Seite mit allen untergeordneten Seiten selektieren
		$items = $db->query('SELECT c.id, c.root_id, c.left_id, c.right_id, c.block_id FROM {pre}menu_items AS p, {pre}menu_items AS c WHERE p.id = \'' . $id . '\' AND c.left_id BETWEEN p.left_id AND p.right_id ORDER BY c.left_id ASC');

		// Überprüfen, ob Seite ein Root-Element ist und ob dies auch so bleiben soll
		if (empty($parent) && $block_id == $items[0]['block_id'] && $db->countRows('*', 'menu_items', 'left_id < ' . $items[0]['left_id'] . ' AND right_id > ' . $items[0]['right_id']) == 0) {
			$bool = $db->update('menu_items', $update_values, 'id = \'' . $id . '\'');
		} else {
			// Überprüfung, falls Seite kein Root-Element ist, aber keine Veränderung vorgenommen werden soll...
			$chk_parent = $db->query('SELECT id FROM {pre}menu_items WHERE left_id < ' . $items[0]['left_id'] . ' AND right_id > ' . $items[0]['right_id'] . ' ORDER BY left_id DESC LIMIT 1');
			if (isset($chk_parent[0]) && $chk_parent[0]['id'] == $parent) {
				$bool = $db->update('menu_items', $update_values, 'id = \'' . $id . '\'');
			// ...ansonsten den Baum bearbeiten...
			} else {
				$bool = false;
				// Differenz zwischen linken und rechten Wert bilden
				$page_diff = $items[0]['right_id'] - $items[0]['left_id'] + 1;

				// Neues Elternelement
				$new_parent = $db->select('root_id, left_id, right_id', 'menu_items', 'id = \'' . $parent . '\'');

				// Knoten werden eigenes Root-Element
				if (empty($new_parent)) {
					// Knoten in anderen Block verschieben
					if ($items[0]['block_id'] != $block_id) {
						$new_block = $db->select('MIN(left_id) AS left_id', 'menu_items', 'block_id = \'' . $block_id . '\'');
						// Falls die Knoten in einen leeren Block verschoben werden sollen,
						// die right_id des letzten Elementes verwenden
						if (empty($new_block) || is_null($new_block[0]['left_id']) === true) {
							$new_block = $db->select('MAX(right_id) AS left_id', 'menu_items');
							$new_block[0]['left_id']+= 1;
						}

						if ($block_id > $items[0]['block_id']) {
							$new_block[0]['left_id'] = $new_block[0]['left_id'] - $page_diff;
						}

						$diff = $new_block[0]['left_id'] - $items[0]['left_id'];
						$root_id = $id;

						$db->link->beginTransaction();
						$db->query('UPDATE {pre}menu_items SET right_id = right_id - ' . $page_diff . ' WHERE left_id < ' . $items[0]['left_id'] . ' AND right_id > ' . $items[0]['right_id'], 0);
						$db->query('UPDATE {pre}menu_items SET left_id = left_id - ' . $page_diff . ', right_id = right_id - ' . $page_diff . ' WHERE left_id > ' . $items[0]['right_id'], 0);
						$db->query('UPDATE {pre}menu_items SET left_id = left_id + ' . $page_diff . ', right_id = right_id + ' . $page_diff . ' WHERE left_id >= ' . $new_block[0]['left_id'], 0);
					// Element zum neuen Elternknoten machen
					} else {
						$new_parent = $db->select('MAX(right_id) AS right_id', 'menu_items', 'block_id =  \'' . $items[0]['block_id'] . '\'');

						$diff = $new_parent[0]['right_id'] - $items[0]['right_id'];
						$root_id = $id;

						$db->link->beginTransaction();
						$db->query('UPDATE {pre}menu_items SET right_id = right_id - ' . $page_diff . ' WHERE left_id < ' . $items[0]['left_id'] . ' AND right_id > ' . $items[0]['right_id'], 0);
						$db->query('UPDATE {pre}menu_items SET left_id = left_id - ' . $page_diff . ', right_id = right_id - ' . $page_diff . ' WHERE left_id > ' . $items[0]['right_id'] . ' AND block_id = \'' . $items[0]['block_id'] . '\'', 0);
					}
				// Knoten werden wieder Kinder von einem anderen Knoten
				} else {
					// Teilbaum nach unten...
					if ($new_parent[0]['left_id'] > $items[0]['left_id']) {
						$new_parent[0]['left_id'] = $new_parent[0]['left_id'] - $page_diff;
						$new_parent[0]['right_id'] = $new_parent[0]['right_id'] - $page_diff;
					}

					$diff = $new_parent[0]['left_id'] - $items[0]['left_id'] + 1;
					$root_id = $new_parent[0]['root_id'];

					$db->link->beginTransaction();
					$db->query('UPDATE {pre}menu_items SET right_id = right_id - ' . $page_diff . ' WHERE left_id < ' . $items[0]['left_id'] . ' AND right_id > ' . $items[0]['right_id'], 0);
					$db->query('UPDATE {pre}menu_items SET left_id = left_id - ' . $page_diff . ', right_id = right_id - ' . $page_diff . ' WHERE left_id > ' . $items[0]['right_id'], 0);
					$db->query('UPDATE {pre}menu_items SET right_id = right_id + ' . $page_diff . ' WHERE left_id <= ' . $new_parent[0]['left_id'] . ' AND right_id >= ' . $new_parent[0]['right_id'], 0);
					$db->query('UPDATE {pre}menu_items SET left_id = left_id + ' . $page_diff . ', right_id = right_id + ' . $page_diff . ' WHERE left_id > ' . $new_parent[0]['left_id'], 0);
				}

				// Einträge aktualisieren
				$c_items = count($items);
				for ($i = 0; $i < $c_items; ++$i) {
					$bool = $db->query('UPDATE {pre}menu_items SET block_id = \'' . $block_id . '\', root_id = \'' . $root_id . '\', left_id = ' . ($items[$i]['left_id'] + $diff) . ', right_id = ' . ($items[$i]['right_id'] + $diff) . ' WHERE id = \'' . $items[$i]['id'] . '\'', 0);
					if ($bool === false)
						break;
				}
				$db->update('menu_items', $update_values, 'id = \'' . $id . '\'');
				$db->link->commit();
			}
		}
		return $bool;
	}
	return false;
}
/**
 * Auflistung der Seiten
 *
 * @param integer $parent_id
 *  ID des Elternknotens
 * @param integer $left_id
 * @param integer $right_id
 * @return array
 */
function menuItemsList($parent_id = 0, $left_id = 0, $right_id = 0) {
	static $pages = array();

	// Menüpunkte einbinden
	if (empty($pages))
		$pages = getMenuItemsCache();

	$output = array();

	if (count($pages) > 0) {
		$i = 0;
		foreach($pages as $row) {
			if (!($row['left_id'] >= $left_id && $row['right_id'] <= $right_id)) {
				$row['selected'] = selectEntry('parent', $row['id'], $parent_id);
				$row['spaces'] = str_repeat('&nbsp;&nbsp;', $row['level']);

				// Titel für den aktuellen Block setzen
				$output[$row['block_title']][$i] = $row;
				++$i;
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
		$items = getMenuItemsCache();
		$c_items = count($items);
		$hide_until = 0;
		$visible_items = array();

		for ($i = 0; $i < $c_items; ++$i) {
			if ($items[$i]['block_name'] == $block) {
				// Nicht anzuzeigende Menüpunkte
				if ($items[$i]['display'] == 0 && $items[$i]['right_id'] > $hide_until)
					$hide_until = $items[$i]['right_id'];
				// Checken, ob der Menüpunkt im angeforderten Block liegt und ob dieser veröffentlicht ist
				if ($items[$i]['display'] == 1 && $items[$i]['right_id'] > $hide_until)
					$visible_items[] = $items[$i];
			}
		}

		$c_items = count($visible_items);

		if ($c_items > 0) {
			global $db, $uri;

			// Selektion nur vornehmen, wenn man sich im Frontend befindet
			if (defined('IN_ADM') === false) {
				$in = "'" . $uri->query . "', '" . $uri->mod . '/' . $uri->file . "/', '" . $uri->mod . "'";
				$select = $db->query('SELECT m.left_id FROM {pre}menu_items AS m JOIN {pre}menu_items_blocks AS b ON(m.block_id = b.id) WHERE b.index_name = \'' . $block . '\' AND m.uri IN(' . $in . ') ORDER BY LENGTH(m.uri) DESC');
			}

			$navbar[$block] = '';

			for ($i = 0; $i < $c_items; ++$i) {
				$css = 'navi-' . $visible_items[$i]['id'];
				// Menüpunkt selektieren
				if (isset($select[0]) &&
						$visible_items[$i]['left_id'] <= $select[0]['left_id'] &&
						$visible_items[$i]['right_id'] > $select[0]['left_id']) {
					$css.= ' selected';
				}

				// Link zusammenbauen
				$href = $visible_items[$i]['mode'] == 1 || $visible_items[$i]['mode'] == 2 || $visible_items[$i]['mode'] == 4 ? $uri->route($visible_items[$i]['uri'], 1) : $visible_items[$i]['uri'];
				$target = $visible_items[$i]['target'] == 2 ? ' onclick="window.open(this.href); return false"' : '';
				$link = '<a href="' . $href . '" class="' . $css . '"' . $target . '>' . $db->escape($visible_items[$i]['title'], 3) . '</a>';

				// Falls für Knoten Kindelemente vorhanden sind, neue Unterliste erstellen
				if (isset($visible_items[$i + 1]) && $visible_items[$i + 1]['level'] > $visible_items[$i]['level']) {
					$navbar[$block].= '<li>' . $link . '<ul class="navigation-' . $block . '-subnav-' . $visible_items[$i]['id'] . '">';
					// Elemente ohne Kindelemente
				} else {
					$navbar[$block].= '<li>' . $link . '</li>';
					// Liste für untergeordnete Elemente schließen
					if (isset($visible_items[$i + 1]) && $visible_items[$i + 1]['level'] < $visible_items[$i]['level'] || !isset($visible_items[$i + 1]) && $visible_items[$i]['level'] != '0') {
						// Differenz ermitteln, wieviele Level zwischen dem aktuellen und dem nachfolgendem Element liegen
						$diff = (isset($visible_items[$i + 1]['level']) ? $visible_items[$i]['level'] - $visible_items[$i + 1]['level'] : $visible_items[$i]['level']) * 2;
						for ($diff; $diff > 0; --$diff) {
							$navbar[$block].= ($diff % 2 == 0 ? '</ul>' : '</li>');
						}
					}
				}
			}
			$navbar[$block] = !empty($navbar[$block]) ? '<ul class="navigation-' . $block . '">' . $navbar[$block] . '</ul>' : '';
			return $navbar[$block];
		}
		return '';
	}
}