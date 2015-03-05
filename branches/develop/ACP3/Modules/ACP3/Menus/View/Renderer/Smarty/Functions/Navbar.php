<?php
namespace ACP3\Modules\ACP3\Menus\View\Renderer\Smarty\Functions;

use ACP3\Core;
use ACP3\Core\View\Renderer\Smarty\Functions\AbstractFunction;
use ACP3\Modules\ACP3\Menus;

/**
 * Class Navbar
 * @package ACP3\Modules\ACP3\Menus\View\Renderer\Smarty\Functions
 */
class Navbar extends AbstractFunction
{
    /**
     * @var array
     */
    protected $menuItems = [];
    /**
     * @var array
     */
    protected $navbar = [];
    /**
     * @var \ACP3\Core\Request
     */
    protected $request;
    /**
     * @var \ACP3\Core\Router
     */
    protected $router;
    /**
     * @var \ACP3\Modules\ACP3\Menus\Model
     */
    protected $menusModel;
    /**
     * @var \ACP3\Modules\ACP3\Menus\Cache
     */
    protected $menusCache;

    /**
     * @param \ACP3\Core\Request        $request
     * @param \ACP3\Core\Router         $router
     * @param \ACP3\Modules\ACP3\Menus\Model $menusModel
     * @param \ACP3\Modules\ACP3\Menus\Cache $menusCache
     */
    public function __construct(
        Core\Request $request,
        Core\Router $router,
        Menus\Model $menusModel,
        Menus\Cache $menusCache
    )
    {
        $this->request = $request;
        $this->router = $router;
        $this->menusModel = $menusModel;
        $this->menusCache = $menusCache;
    }

    /**
     * @inheritdoc
     */
    public function getPluginName()
    {
        return 'navbar';
    }

    /**
     * @inheritdoc
     */
    public function process(array $params, \Smarty_Internal_Template $smarty)
    {
        return $this->_processNavbar(
            $params['block'],
            isset($params['use_bootstrap']) ? (bool)$params['use_bootstrap'] : true,
            !empty($params['class']) ? $params['class'] : '',
            !empty($params['dropdownItemClass']) ? $params['dropdownItemClass'] : '',
            !empty($params['tag']) ? $params['tag'] : 'ul',
            isset($params['itemTag']) ? $params['itemTag'] : 'li',
            !empty($params['dropdownWrapperTag']) ? $params['dropdownWrapperTag'] : 'li',
            !empty($params['classLink']) ? $params['classLink'] : '',
            !empty($params['inlineStyles']) ? $params['inlineStyles'] : ''
        );
    }

    /**
     * Verarbeitet die Navigationsleiste und selektiert die aktuelle Seite,
     * falls diese sich ebenfalls in der Navigationsleiste befindet
     *
     * @param string  $menu
     *    Name des Blocks, für welchen die Navigationspunkte ausgegeben werden sollen
     * @param boolean $useBootstrap
     * @param string  $class
     * @param string  $dropdownItemClass
     * @param string  $tag
     * @param string  $itemTag
     * @param string  $dropdownWrapperTag
     * @param string  $linkCss
     * @param string  $inlineStyles
     *
     * @return string
     */
    protected function _processNavbar(
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

                    if ($items[$i]['mode'] == 1 || $items[$i]['mode'] == 2 || $items[$i]['mode'] == 4) {
                        $href = $this->router->route($items[$i]['uri']);
                    } else {
                        $href = $items[$i]['uri'];
                    }

                    $target = $items[$i]['target'] == 2 ? ' target="_blank"' : '';
                    $attributes = '';
                    $attributes .= !empty($linkCss) ? ' class="' . $linkCss . '"' : '';

                    // Falls für Knoten Kindelemente vorhanden sind, neue Unterliste erstellen
                    if (isset($items[$i + 1]) && $items[$i + 1]['level'] > $items[$i]['level']) {
                        $caret = $subNavbarCss = '';
                        // Special styling for bootstrap enabled navigation bars
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

                        // Close the list of child elements
                        if ((isset($items[$i + 1]) && $items[$i + 1]['level'] < $items[$i]['level']) ||
                            (!isset($items[$i + 1]) && $items[$i]['level'] != '0')) {
                            // Calculate, how many levels between the current and the next element are
                            $diff = $items[$i]['level'];
                            if (isset($items[$i + 1]['level'])) {
                                $diff -= $items[$i + 1]['level'];
                            }
                            $diff *= 2;

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
     *
     * @return int
     */
    protected function _selectMenuItem($menu)
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
            return (int)$this->menusModel->getLeftIdByUris($menu, $in);
        }

        return 0;
    }
}