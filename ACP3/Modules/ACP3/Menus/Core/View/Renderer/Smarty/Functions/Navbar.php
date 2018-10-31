<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Menus\Core\View\Renderer\Smarty\Functions;

use ACP3\Core;
use ACP3\Core\View\Renderer\Smarty\Functions\AbstractFunction;
use ACP3\Modules\ACP3\Menus;
use ACP3\Modules\ACP3\Menus\Helpers\MenuConfiguration;

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
     * @var \ACP3\Core\Http\RequestInterface
     */
    protected $request;
    /**
     * @var \ACP3\Core\Router\RouterInterface
     */
    protected $router;
    /**
     * @var \ACP3\Modules\ACP3\Menus\Model\Repository\MenuItemRepository
     */
    protected $menuItemRepository;
    /**
     * @var \ACP3\Modules\ACP3\Menus\Cache
     */
    protected $menusCache;

    /**
     * Navbar constructor.
     *
     * @param \ACP3\Core\Http\RequestInterface                             $request
     * @param \ACP3\Core\Router\RouterInterface                            $router
     * @param \ACP3\Modules\ACP3\Menus\Model\Repository\MenuItemRepository $menuItemRepository
     * @param \ACP3\Modules\ACP3\Menus\Cache                               $menusCache
     */
    public function __construct(
        Core\Http\RequestInterface $request,
        Core\Router\RouterInterface $router,
        Menus\Model\Repository\MenuItemRepository $menuItemRepository,
        Menus\Cache $menusCache
    ) {
        $this->request = $request;
        $this->router = $router;
        $this->menuItemRepository = $menuItemRepository;
        $this->menusCache = $menusCache;
    }

    /**
     * {@inheritdoc}
     */
    public function getExtensionName()
    {
        return 'navbar';
    }

    /**
     * {@inheritdoc}
     *
     * @throws \Doctrine\DBAL\DBALException
     */
    public function __invoke(array $params, \Smarty_Internal_Template $smarty)
    {
        return $this->getMenuByKey(
            $params['block'],
            $this->createMenuConfiguration($params)
        );
    }

    /**
     * @param array $params
     *
     * @return \ACP3\Modules\ACP3\Menus\Helpers\MenuConfiguration
     */
    private function createMenuConfiguration(array $params): MenuConfiguration
    {
        return new MenuConfiguration(
            isset($params['use_bootstrap']) ? (bool) $params['use_bootstrap'] : true,
            !empty($params['class']) ? $params['class'] : '',
            !empty($params['dropdownItemClass']) ? $params['dropdownItemClass'] : '',
            !empty($params['tag']) ? $params['tag'] : 'ul',
            $params['itemTag'] ?? 'li',
            !empty($params['dropdownWrapperTag']) ? $params['dropdownWrapperTag'] : 'li',
            !empty($params['classLink']) ? $params['classLink'] : '',
            !empty($params['inlineStyles']) ? $params['inlineStyles'] : ''
        );
    }

    /**
     * Verarbeitet die Navigationsleiste und selektiert die aktuelle Seite,
     * falls diese sich ebenfalls in der Navigationsleiste befindet.
     *
     * @param string                                             $menuName
     * @param \ACP3\Modules\ACP3\Menus\Helpers\MenuConfiguration $menuConfig
     *
     * @return string
     *
     * @throws \Doctrine\DBAL\DBALException
     */
    protected function getMenuByKey(
        string $menuName,
        MenuConfiguration $menuConfig
    ) {
        $cacheKey = $this->buildMenuCacheKey($menuName, $menuConfig);
        if (isset($this->menus[$cacheKey])) {
            return $this->menus[$cacheKey];
        }

        return $this->generateMenu($menuName, $menuConfig);
    }

    /**
     * @param string                          $menuName
     * @param Menus\Helpers\MenuConfiguration $menuConfig
     *
     * @return string
     */
    protected function buildMenuCacheKey(string $menuName, MenuConfiguration $menuConfig)
    {
        return $menuName . ':' . $menuConfig->__toString();
    }

    /**
     * @param string                                             $menuName
     * @param \ACP3\Modules\ACP3\Menus\Helpers\MenuConfiguration $menuConfig
     *
     * @return string
     *
     * @throws \Doctrine\DBAL\DBALException
     */
    protected function generateMenu(string $menuName, MenuConfiguration $menuConfig)
    {
        $items = $this->menusCache->getVisibleMenuItems($menuName);
        $cItems = \count($items);

        if ($cItems > 0) {
            $selected = $this->selectMenuItem($menuName);

            $cacheKey = $this->buildMenuCacheKey($menuName, $menuConfig);

            $this->menus[$cacheKey] = '';

            for ($i = 0; $i < $cItems; ++$i) {
                if (isset($items[$i + 1]) && $items[$i + 1]['level'] > $items[$i]['level']) {
                    $this->menus[$cacheKey] .= $this->processMenuItemWithChildren(
                        $menuName,
                        $menuConfig,
                        $items[$i],
                        $this->getMenuItemSelector($items[$i], $selected)
                    );
                } else {
                    $this->menus[$cacheKey] .= $this->processMenuItemWithoutChildren(
                        $menuConfig,
                        $items[$i],
                        $this->getMenuItemSelector($items[$i], $selected)
                    );
                    $this->menus[$cacheKey] .= $this->closeOpenedMenus(
                        $menuConfig,
                        $items,
                        $i
                    );
                }
            }

            if (!empty($this->menus[$cacheKey])) {
                $this->menus[$cacheKey] = \sprintf(
                    '<%1$s%2$s>%3$s</%1$s>',
                    $menuConfig->getTag(),
                    $this->prepareMenuHtmlAttributes($menuName, $menuConfig),
                    $this->menus[$cacheKey]
                );
            } else {
                $this->menus[$cacheKey] = '';
            }

            return $this->menus[$cacheKey];
        }

        return '';
    }

    /**
     * @param string $menuName
     *
     * @return int
     *
     * @throws \Doctrine\DBAL\DBALException
     */
    protected function selectMenuItem(string $menuName)
    {
        if ($this->request->getArea() !== Core\Controller\AreaEnum::AREA_ADMIN) {
            $in = [
                $this->request->getQuery(),
                $this->request->getUriWithoutPages(),
                $this->request->getFullPath(),
                $this->request->getModuleAndController(),
                $this->request->getModule(),
            ];

            return $this->menuItemRepository->getLeftIdByUris($menuName, $in);
        }

        return 0;
    }

    /**
     * @param \ACP3\Modules\ACP3\Menus\Helpers\MenuConfiguration $menuConfig
     * @param array                                              $item
     * @param string                                             $cssSelectors
     *
     * @return string
     */
    protected function processMenuItemWithoutChildren(MenuConfiguration $menuConfig, array $item, string $cssSelectors)
    {
        $link = \sprintf(
            '<a href="%1$s"%2$s%3$s>%4$s</a>',
            $this->getMenuItemHref($item['mode'], $item['uri']),
            $this->getMenuItemHrefTarget($item['target']),
            $this->prepareMenuItemHtmlAttributes($menuConfig),
            $item['title']
        );

        if ($menuConfig->getItemTag() === '') {
            return $link;
        }

        return \sprintf('<%1$s class="%2$s">%3$s</%1$s>', $menuConfig->getItemTag(), $cssSelectors, $link);
    }

    /**
     * @param string                                             $menuName
     * @param \ACP3\Modules\ACP3\Menus\Helpers\MenuConfiguration $menuConfig
     * @param array                                              $item
     * @param string                                             $cssSelectors
     *
     * @return string
     */
    protected function processMenuItemWithChildren(
        string $menuName,
        MenuConfiguration $menuConfig,
        array $item,
        string $cssSelectors
    ) {
        $attributes = $this->prepareMenuItemHtmlAttributes($menuConfig);
        $caret = $subMenuCss = '';
        // Special styling for bootstrap enabled navigation bars
        if ($menuConfig->isUseBootstrap() === true) {
            $dropDownItemClassName = 'navigation-' . $menuName . '-subnav-' . $item['id'] . '-dropdown';
            $cssSelectors .= !empty($menuConfig->getDropdownItemSelector()) ? ' ' . $menuConfig->getDropdownItemSelector() : ' dropdown';
            $cssSelectors .= ' ' . $dropDownItemClassName;
            $caret = $item['level'] == 0 ? ' <b class="caret"></b>' : '';
            $attributes .= $item['level'] == 0 ? '  data-target=".' . $dropDownItemClassName . '"' : '';
            $attributes .= ' class="dropdown-toggle" data-toggle="dropdown"';
            $subMenuCss = 'dropdown-menu ';
        }

        $link = \sprintf(
            '<a href="%1$s"%2$s%3$s>%4$s%5$s</a>',
            $this->getMenuItemHref($item['mode'], $item['uri']),
            $this->getMenuItemHrefTarget($item['target']),
            $attributes,
            $item['title'],
            $caret
        );

        return \sprintf(
            '<%1$s class="%2$s">%3$s<ul class="%4$snavigation-%5$s-subnav-%6$d">',
            $menuConfig->getDropdownWrapperTag(),
            $cssSelectors,
            $link,
            $subMenuCss,
            $menuName,
            $item['id']
        );
    }

    /**
     * Close the list of child elements.
     *
     * @param \ACP3\Modules\ACP3\Menus\Helpers\MenuConfiguration $menuConfig
     * @param array                                              $items
     * @param int                                                $currentIndex
     *
     * @return string
     */
    protected function closeOpenedMenus(MenuConfiguration $menuConfig, array $items, int $currentIndex)
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
    protected function getMenuItemHref(int $mode, string $uri)
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
    protected function getMenuItemHrefTarget(string $target)
    {
        return $target == 2 ? ' target="_blank"' : '';
    }

    /**
     * @param array $items
     * @param int   $currentIndex
     *
     * @return int
     */
    protected function calculateChildParentLevelDiff(array $items, int $currentIndex)
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
    protected function getMenuItemSelector(array $item, int $selectedItemValue)
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
     * @param string                                             $menuName
     * @param \ACP3\Modules\ACP3\Menus\Helpers\MenuConfiguration $menuConfig
     *
     * @return string
     */
    protected function prepareMenuHtmlAttributes(string $menuName, MenuConfiguration $menuConfig)
    {
        $bootstrapSelector = $menuConfig->isUseBootstrap() === true ? ' nav navbar-nav' : '';
        $navigationSelectors = !empty($menuConfig->getSelector()) ? ' ' . $menuConfig->getSelector() : $bootstrapSelector;
        $attributes = ' class="navigation-' . $menuName . $navigationSelectors . '"';
        $attributes .= !empty($menuConfig->getInlineStyle()) ? ' style="' . $menuConfig->getInlineStyle() . '"' : '';

        return $attributes;
    }

    /**
     * @param \ACP3\Modules\ACP3\Menus\Helpers\MenuConfiguration $menuConfig
     *
     * @return string
     */
    protected function prepareMenuItemHtmlAttributes(MenuConfiguration $menuConfig)
    {
        return !empty($menuConfig->getLinkSelector()) ? ' class="' . $menuConfig->getLinkSelector() . '"' : '';
    }
}
