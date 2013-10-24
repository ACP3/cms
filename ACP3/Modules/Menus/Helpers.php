<?php

/**
 * Menu bars
 *
 * @author Tino Goratsch
 * @package ACP3
 * @subpackage Modules
 */

namespace ACP3\Modules\Menus;

use ACP3\Core;

abstract class Helpers {

	/**
	 * Erstellt den Cache für die Menüpunkte
	 *
	 * @return boolean
	 */
	public static function setMenuItemsCache()
	{
		$items = Core\Registry::get('Db')->fetchAll('SELECT n.*, COUNT(*)-1 AS level, ROUND((n.right_id - n.left_id - 1) / 2) AS children FROM ' . DB_PRE . 'menu_items AS p, ' . DB_PRE . 'menu_items AS n WHERE n.left_id BETWEEN p.left_id AND p.right_id GROUP BY n.left_id ORDER BY n.left_id');
		$c_items = count($items);

		if ($c_items > 0) {
			$blocks = Core\Registry::get('Db')->fetchAll('SELECT id, title, index_name FROM ' . DB_PRE . 'menus');
			$c_blocks = count($blocks);

			for ($i = 0; $i < $c_blocks; ++$i) {
				self::setVisibleMenuItemsCache($blocks[$i]['index_name']);
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
				Core\Registry::get('Lang')->t('menus', 'module'),
				Core\Registry::get('Lang')->t('menus', 'dynamic_page'),
				Core\Registry::get('Lang')->t('menus', 'hyperlink'),
				Core\Registry::get('Lang')->t('menus', 'article')
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
		return Core\Cache::create('items', $items, 'menus');
	}

	/**
	 * Bindet die gecacheten Menüpunkte ein
	 *
	 * @return array
	 */
	public static function getMenuItemsCache()
	{
		if (Core\Cache::check('items', 'menus') === false)
			self::setMenuItemsCache();

		return Core\Cache::output('items', 'menus');
	}

	/**
	 * Erstellt den Cache für die Menüpunkte
	 *
	 * @return boolean
	 */
	public static function setVisibleMenuItemsCache($block)
	{
		$items = Core\Registry::get('Db')->fetchAll('SELECT n.*, COUNT(*)-1 AS level, ROUND((n.right_id - n.left_id - 1) / 2) AS children, b.title AS block_title, b.index_name AS block_name FROM ' . DB_PRE . 'menu_items AS p, ' . DB_PRE . 'menu_items AS n JOIN ' . DB_PRE . 'menus AS b ON(n.block_id = b.id) WHERE b.index_name = ? AND n.display = 1 AND n.left_id BETWEEN p.left_id AND p.right_id GROUP BY n.left_id ORDER BY n.left_id', array($block));
		return Core\Cache::create('visible_items_' . $block, $items, 'menus');
	}

	/**
	 * Bindet die gecacheten Menüpunkte ein
	 *
	 * @return array
	 */
	public static function getVisibleMenuItems($block)
	{
		if (Core\Cache::check('visible_items_' . $block, 'menus') === false)
			self::setVisibleMenuItemsCache($block);

		return Core\Cache::output('visible_items_' . $block, 'menus');
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
	public static function menuItemsList($parent_id = 0, $left_id = 0, $right_id = 0)
	{
		static $pages = array();

		// Menüpunkte einbinden
		if (empty($pages))
			$pages = self::getMenuItemsCache();

		$output = array();

		if (count($pages) > 0) {
			foreach ($pages as $row) {
				if (!($row['left_id'] >= $left_id && $row['right_id'] <= $right_id)) {
					$row['selected'] = Core\Functions::selectEntry('parent', $row['id'], $parent_id);
					$row['spaces'] = str_repeat('&nbsp;&nbsp;', $row['level']);

					// Titel für den aktuellen Block setzen
					$output[$row['block_name']]['title'] = $row['block_title'];
					$output[$row['block_name']]['menu_id'] = $row['block_id'];
					$output[$row['block_name']]['items'][] = $row;
				}
			}
		}
		return $output;
	}

	/**
	 * Gibt alle Menüleisten zur Benutzung in einem Dropdown-Menü aus
	 *
	 * @param integer $selected
	 * @return array
	 */
	public static function menusDropdown($selected = 0)
	{
		$blocks = Core\Registry::get('Db')->fetchAll('SELECT id, title FROM ' . DB_PRE . 'menus ORDER BY title ASC, id ASC');
		$c_blocks = count($blocks);
		for ($i = 0; $i < $c_blocks; ++$i) {
			$blocks[$i]['selected'] = Core\Functions::selectEntry('block_id', (int) $blocks[$i]['id'], (int) $selected);
		}

		return $blocks;
	}

	/**
	 * Verarbeitet die Navigationsleiste und selektiert die aktuelle Seite,
	 * falls diese sich ebenfalls in der Navigationsleiste befindet
	 *
	 * @param string $block
	 * 	Name des Blocks, für welchen die Navigationspunkte ausgegeben werden sollen
	 * @param boolean $useBootstrap
	 * @param string $class
	 *
	 * @return string
	 */
	public static function processNavbar($block, $useBootstrap = true, $class = '', $dropdownItemClass = '', $tag = 'ul', $itemTag = 'li', $dropdownWrapperTag = 'li', $linkCss = '', $inlineStyles = '')
	{
		static $navbar = array();

		// Navigationsleiste sofort ausgeben, falls diese schon einmal verarbeitet wurde...
		if (isset($navbar[$block])) {
			return $navbar[$block];
			// ...ansonsten Verarbeitung starten
		} else {
			$items = self::getVisibleMenuItems($block);
			$c_items = count($items);

			if ($c_items > 0) {
				// Selektion nur vornehmen, wenn man sich im Frontend befindet
				if (defined('IN_ADM') === false) {
					$in = array(Core\Registry::get('URI')->query, Core\Registry::get('URI')->getCleanQuery(), Core\Registry::get('URI')->mod . '/' . Core\Registry::get('URI')->file . '/', Core\Registry::get('URI')->mod);
					$selected = Core\Registry::get('Db')->executeQuery('SELECT m.left_id FROM ' . DB_PRE . 'menu_items AS m JOIN ' . DB_PRE . 'menus AS b ON(m.block_id = b.id) WHERE b.index_name = ? AND m.uri IN(?) ORDER BY LENGTH(m.uri) DESC', array($block, $in), array(\PDO::PARAM_STR, \Doctrine\DBAL\Connection::PARAM_STR_ARRAY))->fetch(\PDO::FETCH_COLUMN);
				}

				$navbar[$block] = '';

				for ($i = 0; $i < $c_items; ++$i) {
					$css = 'navi-' . $items[$i]['id'];
					// Menüpunkt selektieren
					if (!empty($selected) &&
							$items[$i]['left_id'] <= $selected &&
							$items[$i]['right_id'] > $selected) {
						$css.= ' active';
					}

					// Link zusammenbauen
					$href = $items[$i]['mode'] == 1 || $items[$i]['mode'] == 2 || $items[$i]['mode'] == 4 ? Core\Registry::get('URI')->route($items[$i]['uri']) : $items[$i]['uri'];
					$target = $items[$i]['target'] == 2 ? ' onclick="window.open(this.href); return false"' : '';
					$attributes = '';
					$attributes.= !empty($linkCss) ? ' class="' . $linkCss . '"' : '';

					// Falls für Knoten Kindelemente vorhanden sind, neue Unterliste erstellen
					if (isset($items[$i + 1]) && $items[$i + 1]['level'] > $items[$i]['level']) {
						$caret = $subNavbarCss = '';
						// Special styling for bootstrap enabled navbars
						if ($useBootstrap === true) {
							$css.= !empty($dropdownItemClass) ? ' ' . $dropdownItemClass : ($items[$i]['level'] == 0 ? ' dropdown' : ' dropdown-submenu');
							$caret = $items[$i]['level'] == 0 ? ' <b class="caret"></b>' : '';
							$attributes.= $items[$i]['level'] == 0 ? '  data-target="#"' : '';
							$attributes.= ' class="dropdown-toggle" data-toggle="dropdown"';
							$subNavbarCss = 'dropdown-menu ';
						}

						$link = sprintf('<a href="%1$s"%2$s%3$s>%4$s%5$s</a>', $href, $target, $attributes, $items[$i]['title'], $caret);
						$navbar[$block].= sprintf('<%1$s class="%2$s">%3$s<ul class="%4$snavigation-%5$s-subnav-%6$d">', $dropdownWrapperTag, $css, $link, $subNavbarCss, $block, $items[$i]['id']);
						// Elemente ohne Kindelemente
					} else {
						$link = sprintf('<a href="%1$s"%2$s%3$s>%4$s</a>', $href, $target, $attributes, $items[$i]['title']);
						$navbar[$block].= $itemTag === '' ? $link : sprintf('<%1$s class="%2$s">%3$s</%1$s>', $itemTag, $css, $link);

						// Liste für untergeordnete Elemente schließen
						if (isset($items[$i + 1]) && $items[$i + 1]['level'] < $items[$i]['level'] || !isset($items[$i + 1]) && $items[$i]['level'] != '0') {
							// Differenz ermitteln, wieviele Level zwischen dem aktuellen und dem nachfolgendem Element liegen
							$diff = (isset($items[$i + 1]['level']) ? $items[$i]['level'] - $items[$i + 1]['level'] : $items[$i]['level']) * 2;
							for ($diff; $diff > 0; --$diff) {
								$navbar[$block].= ($diff % 2 == 0 ? '</ul>' : '</' . $dropdownWrapperTag . '>');
							}
						}
					}
				}
				$attributes = ' class="navigation-' . $block . (!empty($class) ? ' ' . $class : ($useBootstrap === true ? ' nav' : '')) . '"';
				$attributes.= !empty($inlineStyles) ? ' style="' . $inlineStyles . '"' : '';
				$navbar[$block] = !empty($navbar[$block]) ? sprintf('<%1$s%2$s>%3$s</%1$s>', $tag, $attributes, $navbar[$block]) : '';
				return $navbar[$block];
			}
			return '';
		}
	}

}