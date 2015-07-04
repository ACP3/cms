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
    protected $menus = [];
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
     * @param \ACP3\Core\Request             $request
     * @param \ACP3\Core\Router              $router
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
        return $this->getMenuByKey(
            $params['block'],
            new Menus\Helpers\MenuConfiguration(
                isset($params['use_bootstrap']) ? (bool)$params['use_bootstrap'] : true,
                !empty($params['class']) ? $params['class'] : '',
                !empty($params['dropdownItemClass']) ? $params['dropdownItemClass'] : '',
                !empty($params['tag']) ? $params['tag'] : 'ul',
                isset($params['itemTag']) ? $params['itemTag'] : 'li',
                !empty($params['dropdownWrapperTag']) ? $params['dropdownWrapperTag'] : 'li',
                !empty($params['classLink']) ? $params['classLink'] : '',
                !empty($params['inlineStyles']) ? $params['inlineStyles'] : ''
            )
        );
    }

    /**
     * Verarbeitet die Navigationsleiste und selektiert die aktuelle Seite,
     * falls diese sich ebenfalls in der Navigationsleiste befindet
     *
     * @param string                                             $menu
     * @param \ACP3\Modules\ACP3\Menus\Helpers\MenuConfiguration $menuConfig
     *
     * @return string
     */
    protected function getMenuByKey(
        $menu,
        Menus\Helpers\MenuConfiguration $menuConfig)
    {
        // Navigationsleiste sofort ausgeben, falls diese schon einmal verarbeitet wurde...
        if (isset($this->menus[$menu])) {
            return $this->menus[$menu];
        } else { // ...ansonsten Verarbeitung starten
            return $this->generateMenu($menu, $menuConfig);
        }
    }

    /**
     * @param                                                      $menu
     * @param \ACP3\Modules\ACP3\Menus\Helpers\MenuConfiguration   $menuConfig
     *
     * @return string
     */
    protected function generateMenu($menu, Menus\Helpers\MenuConfiguration $menuConfig)
    {
        $items = $this->menusCache->getVisibleMenuItems($menu);
        $c_items = count($items);

        if ($c_items > 0) {
            $selected = $this->_selectMenuItem($menu);

            $this->menus[$menu] = '';

            for ($i = 0; $i < $c_items; ++$i) {
                $css = $this->getMenuItemSelector($items[$i], $selected);

                if (isset($items[$i + 1]) && $items[$i + 1]['level'] > $items[$i]['level']) {
                    $this->menus[$menu] .= $this->_processMenuItemWithChildren(
                        $menu,
                        $menuConfig,
                        $items[$i],
                        $css
                    );
                } else {
                    $this->menus[$menu] .= $this->_processMenuItemWithoutChildren(
                        $menuConfig,
                        $items[$i],
                        $css
                    );
                    $this->menus[$menu] .= $this->closeOpenedMenus(
                        $menuConfig,
                        $items,
                        $i
                    );
                }
            }

            if (!empty($this->menus[$menu])) {
                $this->menus[$menu] = sprintf(
                    '<%1$s%2$s>%3$s</%1$s>',
                    $menuConfig->getTag(),
                    $this->prepareMenuHtmlAttributes($menu, $menuConfig),
                    $this->menus[$menu]
                );
            } else {
                $this->menus[$menu] = '';
            }

            return $this->menus[$menu];
        }
        return '';
    }

    /**
     * @param string $menu
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

    /**
     * @param \ACP3\Modules\ACP3\Menus\Helpers\MenuConfiguration $menuConfig
     * @param array                                              $item
     * @param string                                             $css
     *
     * @return string
     */
    protected function _processMenuItemWithoutChildren(Menus\Helpers\MenuConfiguration $menuConfig, $item, $css)
    {
        $href = $this->getMenuItemHref($item['mode'], $item['uri']);
        $target = $this->getMenuItemHrefTarget($item['target']);
        $link = sprintf(
            '<a href="%1$s"%2$s%3$s>%4$s</a>',
            $href,
            $target,
            $this->prepareMenuItemHtmlAttributes($menuConfig),
            $item['title']
        );

        if ($menuConfig->getItemTag() === '') {
            return $link;
        }

        return sprintf('<%1$s class="%2$s">%3$s</%1$s>', $menuConfig->getItemTag(), $css, $link);
    }

    /**
     * @param string                                             $menu
     * @param \ACP3\Modules\ACP3\Menus\Helpers\MenuConfiguration $menuConfig
     * @param array                                              $item
     * @param string                                             $css
     *
     * @return string
     */
    protected function _processMenuItemWithChildren($menu, Menus\Helpers\MenuConfiguration $menuConfig, $item, $css)
    {
        $attributes = $this->prepareMenuItemHtmlAttributes($menuConfig);
        $caret = $subMenuCss = '';
        // Special styling for bootstrap enabled navigation bars
        if ($menuConfig->isUseBootstrap() === true) {
            $dropDownItemClassName = 'navigation-' . $menu . '-subnav-' . $item['id'] . '-dropdown';
            $css .= !empty($menuConfig->getDropdownItemSelector()) ? ' ' . $menuConfig->getDropdownItemSelector() : ' dropdown';
            $css .= $dropDownItemClassName;
            $caret = $item['level'] == 0 ? ' <b class="caret"></b>' : '';
            $attributes .= $item['level'] == 0 ? '  data-target=".' . $dropDownItemClassName . '"' : '';
            $attributes .= ' class="dropdown-toggle" data-toggle="dropdown"';
            $subMenuCss = 'dropdown-menu ';
        }

        $href = $this->getMenuItemHref($item['mode'], $item['uri']);
        $target = $this->getMenuItemHrefTarget($item['target']);
        $link = sprintf('<a href="%1$s"%2$s%3$s>%4$s%5$s</a>', $href, $target, $attributes, $item['title'], $caret);

        return sprintf(
            '<%1$s class="%2$s">%3$s<ul class="%4$snavigation-%5$s-subnav-%6$d">',
            $menuConfig->getDropdownWrapperTag(),
            $css,
            $link,
            $subMenuCss,
            $menu,
            $item['id']
        );
    }

    /**
     * Close the list of child elements
     *
     * @param \ACP3\Modules\ACP3\Menus\Helpers\MenuConfiguration $menuConfig
     * @param array                                              $items
     * @param int                                                $currentIndex
     *
     * @return string
     */
    protected function closeOpenedMenus(Menus\Helpers\MenuConfiguration $menuConfig, $items, $currentIndex)
    {
        $data = '';
        if ((isset($items[$currentIndex + 1]) && $items[$currentIndex + 1]['level'] < $items[$currentIndex]['level']) ||
            (!isset($items[$currentIndex + 1]) && $items[$currentIndex]['level'] != '0')
        ) {
            // Calculate, how many levels between the current and the next element are
            $diff = $this->calculateChildParentLevelDiff($items, $currentIndex);

            for (; $diff > 0; --$diff) {
                $data .= ($diff % 2 == 0 ? '</ul>' : '</' . $menuConfig->getDropdownWrapperTag() . '>');
            }
        }

        return $data;
    }

    /**
     * @param int    $mode
     * @param string $uri
     *
     * @return string
     */
    protected function getMenuItemHref($mode, $uri)
    {
        if ($mode == 1 || $mode == 2 || $mode == 4) {
            return $this->router->route($uri);
        }

        return $uri;
    }

    /**
     * @param string $target
     *
     * @return string
     */
    protected function getMenuItemHrefTarget($target)
    {
        return $target == 2 ? ' target="_blank"' : '';
    }

    /**
     * @param array $items
     * @param int   $currentIndex
     *
     * @return int
     */
    protected function calculateChildParentLevelDiff($items, $currentIndex)
    {
        $diff = $items[$currentIndex]['level'];
        if (isset($items[$currentIndex + 1]['level'])) {
            $diff -= $items[$currentIndex + 1]['level'];
        }
        $diff *= 2;
        return $diff;
    }

    /**
     * @param array $item
     * @param int   $selectedItemValue
     *
     * @return string
     */
    protected function getMenuItemSelector($item, $selectedItemValue)
    {
        $css = 'navi-' . $item['id'];

        if (!empty($selectedItemValue) &&
            $item['left_id'] <= $selectedItemValue &&
            $item['right_id'] > $selectedItemValue
        ) {
            $css .= ' active';
        }

        return $css;
    }

    /**
     * @param                                                      $menu
     * @param \ACP3\Modules\ACP3\Menus\Helpers\MenuConfiguration   $menuConfig
     *
     * @return string
     */
    protected function prepareMenuHtmlAttributes($menu, Menus\Helpers\MenuConfiguration $menuConfig)
    {
        $bootstrapSelector = $menuConfig->isUseBootstrap() === true ? ' nav navbar-nav' : '';
        $vavigationSelectors = !empty($menuConfig->getSelector()) ? ' ' . $menuConfig->getSelector() : $bootstrapSelector;
        $attributes = ' class="navigation-' . $menu . $vavigationSelectors . '"';
        $attributes .= !empty($menuConfig->getInlineStyle()) ? ' style="' . $menuConfig->getInlineStyle() . '"' : '';

        return $attributes;
    }

    /**
     * @param \ACP3\Modules\ACP3\Menus\Helpers\MenuConfiguration $menuConfig
     *
     * @return string
     */
    protected function prepareMenuItemHtmlAttributes(Menus\Helpers\MenuConfiguration $menuConfig)
    {
        $attributes = !empty($menuConfig->getLinkSelector()) ? ' class="' . $menuConfig->getLinkSelector() . '"' : '';
        return $attributes;
    }
}