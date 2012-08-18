<?php
/**
 * Pages
 *
 * @author Tino Goratsch
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

		for ($i = 0; $i < $c_blocks; ++$i) {
			setVisibleMenuItemsCache($blocks[$i]['index_name']);
		}

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
			$items[$i]['mode_formatted'] = str_replace($mode_search, $mode_replace, $items[$i]['mode']);

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
	return ACP3_Cache::create('menu_items', $items);
}
/**
 * Bindet die gecacheten Menüpunkte ein
 *
 * @return array
 */
function getMenuItemsCache()
{
	if (ACP3_Cache::check('menu_items') === false)
		setMenuItemsCache();

	return ACP3_Cache::output('menu_items');
}
/**
 * Erstellt den Cache für die Menüpunkte
 *
 * @return boolean
 */
function setVisibleMenuItemsCache($block) {
	global $db;

	$items = $db->query('SELECT n.*, COUNT(*)-1 AS level, ROUND((n.right_id - n.left_id - 1) / 2) AS children, b.title AS block_title, b.index_name AS block_name FROM {pre}menu_items AS p, {pre}menu_items AS n JOIN {pre}menu_items_blocks AS b ON(n.block_id = b.id) WHERE b.index_name = \'' . $db->escape($block) . '\' AND n.display = 1 AND n.left_id BETWEEN p.left_id AND p.right_id GROUP BY n.left_id ORDER BY n.left_id');

	return ACP3_Cache::create('visible_menu_items_' . $block, $items);
}
/**
 * Bindet die gecacheten Menüpunkte ein
 *
 * @return array
 */
function getVisibleMenuItems($block)
{
	if (ACP3_Cache::check('visible_menu_items_' . $block) === false)
		setVisibleMenuItemsCache($block);

	return ACP3_Cache::output('visible_menu_items_' . $block);
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
 * @param boolean $use_bootstrap
 * @param string $class
 *
 * @return string
 */
function processNavbar($block, $use_bootstrap = true, $class = '') {
	static $navbar = array();

	// Navigationsleiste sofort ausgeben, falls diese schon einmal verarbeitet wurde...
	if (isset($navbar[$block])) {
		return $navbar[$block];
	// ...ansonsten Verarbeitung starten
	} else {
		$items = getVisibleMenuItems($block);
		$c_items = count($items);

		if ($c_items > 0) {
			global $db, $uri;

			// Selektion nur vornehmen, wenn man sich im Frontend befindet
			if (defined('IN_ADM') === false) {
				$in = "'" . $uri->query . "', '" . $uri->getCleanQuery() . "', '" . $uri->mod . '/' . $uri->file . "/', '" . $uri->mod . "'";
				$select = $db->query('SELECT m.left_id FROM {pre}menu_items AS m JOIN {pre}menu_items_blocks AS b ON(m.block_id = b.id) WHERE b.index_name = \'' . $block . '\' AND m.uri IN(' . $in . ') ORDER BY LENGTH(m.uri) DESC');
			}

			$navbar[$block] = '';

			for ($i = 0; $i < $c_items; ++$i) {
				$css = 'navi-' . $items[$i]['id'];
				// Menüpunkt selektieren
				if (isset($select[0]) &&
						$items[$i]['left_id'] <= $select[0]['left_id'] &&
						$items[$i]['right_id'] > $select[0]['left_id']) {
					$css.= ' active';
				}

				// Link zusammenbauen
				$href = $items[$i]['mode'] == 1 || $items[$i]['mode'] == 2 || $items[$i]['mode'] == 4 ? $uri->route($items[$i]['uri'], 1) : $items[$i]['uri'];
				$target = $items[$i]['target'] == 2 ? ' onclick="window.open(this.href); return false"' : '';
				$link = '<a href="' . $href . '"' . $target . '>' . $db->escape($items[$i]['title'], 3) . '</a>';

				// Falls für Knoten Kindelemente vorhanden sind, neue Unterliste erstellen
				if (isset($items[$i + 1]) && $items[$i + 1]['level'] > $items[$i]['level']) {
					$navbar[$block].= '<li class="' . $css . '">' . $link . '<ul class="unstyled navigation-' . $block . '-subnav-' . $items[$i]['id'] . '">';
					// Elemente ohne Kindelemente
				} else {
					$navbar[$block].= '<li class="' . $css . '">' . $link . '</li>';
					// Liste für untergeordnete Elemente schließen
					if (isset($items[$i + 1]) && $items[$i + 1]['level'] < $items[$i]['level'] || !isset($items[$i + 1]) && $items[$i]['level'] != '0') {
						// Differenz ermitteln, wieviele Level zwischen dem aktuellen und dem nachfolgendem Element liegen
						$diff = (isset($items[$i + 1]['level']) ? $items[$i]['level'] - $items[$i + 1]['level'] : $items[$i]['level']) * 2;
						for ($diff; $diff > 0; --$diff) {
							$navbar[$block].= ($diff % 2 == 0 ? '</ul>' : '</li>');
						}
					}
				}
			}
			$navbar[$block] = !empty($navbar[$block]) ? '<ul class="navigation-' . $block . (!empty($class) ? ' ' . $class : '') . ($use_bootstrap === true ? ' nav' : '') . '">' . $navbar[$block] . '</ul>' : '';
			return $navbar[$block];
		}
		return '';
	}
}