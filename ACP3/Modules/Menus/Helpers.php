<?php
namespace ACP3\Modules\Menus;

use ACP3\Core;

/**
 * Class Helpers
 * @package ACP3\Modules\Menus
 */
class Helpers
{
    const ARTICLES_URL_KEY_REGEX = '/^(articles\/index\/details\/id_([0-9]+)\/)$/';

    /**
     * @var array
     */
    protected $menuItems = [];
    /**
     * @var array
     */
    protected $navbar = [];
    /**
     * @var Core\Request
     */
    protected $request;
    /**
     * @var \ACP3\Core\Router
     */
    protected $router;
    /**
     * @var Core\Helpers\Forms
     */
    protected $formsHelper;
    /**
     * @var Model
     */
    protected $menusModel;
    /**
     * @var Cache
     */
    protected $menusCache;

    /**
     * @param Core\Request $request
     * @param Core\Router $router
     * @param Core\Helpers\Forms $formsHelper
     * @param Model $menusModel
     * @param Cache $menusCache
     */
    public function __construct(
        Core\Request $request,
        Core\Router $router,
        Core\Helpers\Forms $formsHelper,
        Model $menusModel,
        Cache $menusCache
    ) {
        $this->request = $request;
        $this->router = $router;
        $this->formsHelper = $formsHelper;
        $this->menusModel = $menusModel;
        $this->menusCache = $menusCache;
    }

    /**
     * Auflistung der Seiten
     *
     * @param integer $parentId
     *  ID des Elternknotens
     * @param integer $leftId
     * @param integer $rightId
     *
     * @return array
     */
    public function menuItemsList($parentId = 0, $leftId = 0, $rightId = 0)
    {
        // Menüpunkte einbinden
        if (empty($this->menuItems)) {
            $this->menuItems = $this->menusCache->getMenuItemsCache();
        }

        $output = [];

        if (count($this->menuItems) > 0) {
            foreach ($this->menuItems as $row) {
                if (!($row['left_id'] >= $leftId && $row['right_id'] <= $rightId)) {
                    $row['selected'] = $this->formsHelper->selectEntry('parent', $row['id'], $parentId);
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
     *
     * @return array
     */
    public function menusDropdown($selected = 0)
    {
        $menus = $this->menusModel->getAllMenus();
        $c_menus = count($menus);
        for ($i = 0; $i < $c_menus; ++$i) {
            $menus[$i]['selected'] = $this->formsHelper->selectEntry('block_id', (int)$menus[$i]['id'], (int)$selected);
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
     * @param string $dropdownItemClass
     * @param string $tag
     * @param string $itemTag
     * @param string $dropdownWrapperTag
     * @param string $linkCss
     * @param string $inlineStyles
     *
     * @return string
     */
    public function processNavbar(
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
        // Navigationsleiste sofort ausgeben, falls diese schon einmal verarbeitet wurde...
        if (isset($this->navbar[$menu])) {
            return $this->navbar[$menu];
        } else { // ...ansonsten Verarbeitung starten
            $items = $this->menusCache->getVisibleMenuItems($menu);
            $c_items = count($items);

            if ($c_items > 0) {
                $selected = $this->_selectMenuItem($menu);

                $this->navbar[$menu] = '';

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
                    $href = $items[$i]['mode'] == 1 || $items[$i]['mode'] == 2 || $items[$i]['mode'] == 4 ? $this->router->route($items[$i]['uri']) : $items[$i]['uri'];
                    $target = $items[$i]['target'] == 2 ? ' target="_blank"' : '';
                    $attributes = '';
                    $attributes .= !empty($linkCss) ? ' class="' . $linkCss . '"' : '';

                    // Falls für Knoten Kindelemente vorhanden sind, neue Unterliste erstellen
                    if (isset($items[$i + 1]) && $items[$i + 1]['level'] > $items[$i]['level']) {
                        $caret = $subNavbarCss = '';
                        // Special styling for bootstrap enabled navbars
                        if ($useBootstrap === true) {
                            $dropDownItemClassName = 'navigation-' . $menu . '-subnav-' . $items[$i]['id'] . '-dropdown';
                            $css .= !empty($dropdownItemClass) ? ' ' . $dropdownItemClass : ' dropdown';
                            $css .= $dropDownItemClassName;
                            $caret = $items[$i]['level'] == 0 ? ' <b class="caret"></b>' : '';
                            $attributes .= $items[$i]['level'] == 0 ? '  data-target=".' . $dropDownItemClassName . '"' : '';
                            $attributes .= ' class="dropdown-toggle" data-toggle="dropdown"';
                            $subNavbarCss = 'dropdown-menu ';
                        }

                        $link = sprintf('<a href="%1$s"%2$s%3$s>%4$s%5$s</a>', $href, $target, $attributes, $items[$i]['title'], $caret);
                        $this->navbar[$menu] .= sprintf('<%1$s class="%2$s">%3$s<ul class="%4$snavigation-%5$s-subnav-%6$d">', $dropdownWrapperTag, $css, $link, $subNavbarCss, $menu, $items[$i]['id']);
                    } else { // Elemente ohne Kindelemente
                        $link = sprintf('<a href="%1$s"%2$s%3$s>%4$s</a>', $href, $target, $attributes, $items[$i]['title']);
                        $this->navbar[$menu] .= $itemTag === '' ? $link : sprintf('<%1$s class="%2$s">%3$s</%1$s>', $itemTag, $css, $link);

                        // Liste für untergeordnete Elemente schließen
                        if (isset($items[$i + 1]) && $items[$i + 1]['level'] < $items[$i]['level'] || !isset($items[$i + 1]) && $items[$i]['level'] != '0') {
                            // Differenz ermitteln, wieviele Level zwischen dem aktuellen und dem nachfolgendem Element liegen
                            $diff = (isset($items[$i + 1]['level']) ? $items[$i]['level'] - $items[$i + 1]['level'] : $items[$i]['level']) * 2;
                            for (; $diff > 0; --$diff) {
                                $this->navbar[$menu] .= ($diff % 2 == 0 ? '</ul>' : '</' . $dropdownWrapperTag . '>');
                            }
                        }
                    }
                }
                $attributes = ' class="navigation-' . $menu . (!empty($class) ? ' ' . $class : ($useBootstrap === true ? ' nav navbar-nav' : '')) . '"';
                $attributes .= !empty($inlineStyles) ? ' style="' . $inlineStyles . '"' : '';
                $this->navbar[$menu] = !empty($this->navbar[$menu]) ? sprintf('<%1$s%2$s>%3$s</%1$s>', $tag, $attributes, $this->navbar[$menu]) : '';
                return $this->navbar[$menu];
            }
            return '';
        }
    }

    /**
     * @param $menu
     * @return int
     */
    private function _selectMenuItem($menu)
    {
        // Selektion nur vornehmen, wenn man sich im Frontend befindet
        if ($this->request->area !== 'admin') {
            $in = [
                $this->request->query,
                $this->request->getUriWithoutPages(),
                $this->request->mod . '/' . $this->request->controller . '/' . $this->request->file . '/',
                $this->request->mod . '/' . $this->request->controller . '/',
                $this->request->mod
            ];
            return (int) $this->menusModel->getLeftIdByUris($menu, $in);
        }

        return 0;
    }
}
