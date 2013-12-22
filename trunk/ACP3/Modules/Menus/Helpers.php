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

abstract class Helpers
{
    protected static $menuItems = array();

    protected static $navbar = array();

    /**
     *
     * @var Model
     */
    protected static $model;

    protected static function _init()
    {
        if (!self::$model) {
            self::$model = new Model(Core\Registry::get('Db'));
        }
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
        self::_init();

        // Menüpunkte einbinden
        if (empty(self::$menuItems)) {
            self::$menuItems = self::$model->getMenuItemsCache();
        }

        $output = array();

        if (count(self::$menuItems) > 0) {
            foreach (self::$menuItems as $row) {
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
        self::_init();

        $menus = self::$model->getAllMenus();
        $c_menus = count($menus);
        for ($i = 0; $i < $c_menus; ++$i) {
            $menus[$i]['selected'] = Core\Functions::selectEntry('block_id', (int)$menus[$i]['id'], (int)$selected);
        }

        return $menus;
    }

    /**
     * Verarbeitet die Navigationsleiste und selektiert die aktuelle Seite,
     * falls diese sich ebenfalls in der Navigationsleiste befindet
     *
     * @param string $menu
     *    Name des Blocks, für welchen die Navigationspunkte ausgegeben werden sollen
     * @param boolean $useBootstrap
     * @param string $class
     *
     * @return string
     */
    public static function processNavbar(
        $menu,
        $useBootstrap = true,
        $class = '',
        $dropdownItemClass = '',
        $tag = 'ul',
        $itemTag = 'li',
        $dropdownWrapperTag = 'li',
        $linkCss = '',
        $inlineStyles = '')
    {
        self::_init();

        $uri = Core\Registry::get('URI');

        // Navigationsleiste sofort ausgeben, falls diese schon einmal verarbeitet wurde...
        if (isset(self::$navbar[$menu])) {
            return self::$navbar[$menu];
            // ...ansonsten Verarbeitung starten
        } else {
            $items = self::$model->getVisibleMenuItems($menu);
            $c_items = count($items);

            if ($c_items > 0) {
                // Selektion nur vornehmen, wenn man sich im Frontend befindet
                if (defined('IN_ADM') === false) {
                    $in = array($uri->query, $uri->getCleanQuery(), $uri->mod . '/' . $uri->file . '/', $uri->mod);
                    $selected = Core\Registry::get('Db')->executeQuery('SELECT m.left_id FROM ' . DB_PRE . Model::TABLE_NAME_ITEMS . ' AS m JOIN ' . DB_PRE . Model::TABLE_NAME . ' AS b ON(m.block_id = b.id) WHERE b.index_name = ? AND m.uri IN(?) ORDER BY LENGTH(m.uri) DESC', array($menu, $in), array(\PDO::PARAM_STR, \Doctrine\DBAL\Connection::PARAM_STR_ARRAY))->fetch(\PDO::FETCH_COLUMN);
                }

                self::$navbar[$menu] = '';

                for ($i = 0; $i < $c_items; ++$i) {
                    $css = 'navi-' . $items[$i]['id'];
                    // Menüpunkt selektieren
                    if (!empty($selected) &&
                        $items[$i]['left_id'] <= $selected &&
                        $items[$i]['right_id'] > $selected
                    ) {
                        $css .= ' active';
                    }

                    // Link zusammenbauen
                    $href = $items[$i]['mode'] == 1 || $items[$i]['mode'] == 2 || $items[$i]['mode'] == 4 ? $uri->route($items[$i]['uri']) : $items[$i]['uri'];
                    $target = $items[$i]['target'] == 2 ? ' onclick="window.open(this.href); return false"' : '';
                    $attributes = '';
                    $attributes .= !empty($linkCss) ? ' class="' . $linkCss . '"' : '';

                    // Falls für Knoten Kindelemente vorhanden sind, neue Unterliste erstellen
                    if (isset($items[$i + 1]) && $items[$i + 1]['level'] > $items[$i]['level']) {
                        $caret = $subNavbarCss = '';
                        // Special styling for bootstrap enabled navbars
                        if ($useBootstrap === true) {
                            $css .= !empty($dropdownItemClass) ? ' ' . $dropdownItemClass : ' dropdown';
                            $caret = $items[$i]['level'] == 0 ? ' <b class="caret"></b>' : '';
                            $attributes .= $items[$i]['level'] == 0 ? '  data-target="#"' : '';
                            $attributes .= ' class="dropdown-toggle" data-toggle="dropdown"';
                            $subNavbarCss = 'dropdown-menu ';
                        }

                        $link = sprintf('<a href="%1$s"%2$s%3$s>%4$s%5$s</a>', $href, $target, $attributes, $items[$i]['title'], $caret);
                        self::$navbar[$menu] .= sprintf('<%1$s class="%2$s">%3$s<ul class="%4$snavigation-%5$s-subnav-%6$d">', $dropdownWrapperTag, $css, $link, $subNavbarCss, $menu, $items[$i]['id']);
                        // Elemente ohne Kindelemente
                    } else {
                        $link = sprintf('<a href="%1$s"%2$s%3$s>%4$s</a>', $href, $target, $attributes, $items[$i]['title']);
                        self::$navbar[$menu] .= $itemTag === '' ? $link : sprintf('<%1$s class="%2$s">%3$s</%1$s>', $itemTag, $css, $link);

                        // Liste für untergeordnete Elemente schließen
                        if (isset($items[$i + 1]) && $items[$i + 1]['level'] < $items[$i]['level'] || !isset($items[$i + 1]) && $items[$i]['level'] != '0') {
                            // Differenz ermitteln, wieviele Level zwischen dem aktuellen und dem nachfolgendem Element liegen
                            $diff = (isset($items[$i + 1]['level']) ? $items[$i]['level'] - $items[$i + 1]['level'] : $items[$i]['level']) * 2;
                            for ($diff; $diff > 0; --$diff) {
                                self::$navbar[$menu] .= ($diff % 2 == 0 ? '</ul>' : '</' . $dropdownWrapperTag . '>');
                            }
                        }
                    }
                }
                $attributes = ' class="navigation-' . $menu . (!empty($class) ? ' ' . $class : ($useBootstrap === true ? ' nav navbar-nav' : '')) . '"';
                $attributes .= !empty($inlineStyles) ? ' style="' . $inlineStyles . '"' : '';
                self::$navbar[$menu] = !empty(self::$navbar[$menu]) ? sprintf('<%1$s%2$s>%3$s</%1$s>', $tag, $attributes, self::$navbar[$menu]) : '';
                return self::$navbar[$menu];
            }
            return '';
        }
    }

}