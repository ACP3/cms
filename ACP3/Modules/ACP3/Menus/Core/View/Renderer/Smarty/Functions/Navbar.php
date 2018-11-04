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
    )
    {
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
            $this->createMenuConfig($params)
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
    ): string
    {
        $cacheKey = $this->buildMenuCacheKey($menuName, $menuConfig);

        if (!isset($this->menus[$cacheKey])) {
            $this->menus[$cacheKey] = $this->generateMenu($menuName, $menuConfig);
        }

        return $this->menus[$cacheKey];
    }

    /**
     * @param string                          $menuName
     * @param Menus\Helpers\MenuConfiguration $menuConfig
     *
     * @return string
     */
    protected function buildMenuCacheKey(string $menuName, MenuConfiguration $menuConfig): string
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
    private function generateMenu(string $menuName, MenuConfiguration $menuConfig): string
    {
        $menuItems = $this->menusCache->getVisibleMenuItems($menuName);

        if (empty($menuItems)) {
            return '';
        }

        $selected = $this->selectMenuItem($menuName);
        $menu = '';

        foreach ($menuItems as $index => $menuItem) {
            if (isset($menuItems[$index + 1]) && $menuItems[$index + 1]['level'] > $menuItem['level']) {
                $menu .= $this->processMenuItemWithChildren(
                    $menuName,
                    $menuConfig,
                    $menuItem,
                    $this->getMenuItemSelectors($menuItem, $selected, $menuConfig)
                );
            } else {
                $menu .= $this->processMenuItemWithoutChildren(
                    $menuConfig,
                    $menuItem,
                    $this->getMenuItemSelectors($menuItem, $selected, $menuConfig)
                );
                $menu .= $this->closeOpenedMenus(
                    $menuConfig,
                    $menuItems,
                    $index
                );
            }
        }

        if (!empty($menu)) {
            return \sprintf(
                '<%1$s%2$s>%3$s</%1$s>',
                $menuConfig->getTag(),
                $this->prepareMenuHtmlAttributes($menuName, $menuConfig),
                $menu
            );
        }

        return $menu;
    }

    /**
     * @param string $menuName
     *
     * @return int
     *
     * @throws \Doctrine\DBAL\DBALException
     */
    private function selectMenuItem(string $menuName): int
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
     * @param string[]                                           $cssSelectors
     *
     * @return string
     */
    private function processMenuItemWithoutChildren(MenuConfiguration $menuConfig, array $item, array $cssSelectors): string
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

        return \sprintf(
            '<%1$s class="%2$s">%3$s</%1$s>',
            $menuConfig->getItemTag(),
            $this->glueCssSelectors($cssSelectors),
            $link
        );
    }

    /**
     * @param string                                             $menuName
     * @param \ACP3\Modules\ACP3\Menus\Helpers\MenuConfiguration $menuConfig
     * @param array                                              $item
     * @param string[]                                           $itemSelectors
     *
     * @return string
     */
    private function processMenuItemWithChildren(
        string $menuName,
        MenuConfiguration $menuConfig,
        array $item,
        array $itemSelectors
    ): string
    {
        $attributes = $this->prepareMenuItemHtmlAttributes($menuConfig, ['dropdown-toggle']);
        // Special styling for bootstrap enabled navigation bars
        if ($menuConfig->isUseBootstrap() === true) {
            $dropDownItemClassName = 'navigation-' . $menuName . '-subnav-' . $item['id'] . '-dropdown';
            $itemSelectors[] = $dropDownItemClassName;
            $itemSelectors = \array_merge($itemSelectors, $menuConfig->getDropdownItemSelector());
            $attributes .= $item['level'] == 0 ? '  data-target=".' . $dropDownItemClassName . '"' : '';
            $attributes .= ' data-toggle="dropdown"';
        }

        $link = \sprintf(
            '<a href="%1$s"%2$s%3$s>%4$s</a>',
            $this->getMenuItemHref($item['mode'], $item['uri']),
            $this->getMenuItemHrefTarget($item['target']),
            $attributes,
            $item['title']
        );

        return \sprintf(
            '<%1$s class="%2$s">%3$s<ul class="navigation-%5$s-subnav-%6$d %4$s">',
            $menuConfig->getDropdownWrapperTag(),
            $this->glueCssSelectors($itemSelectors),
            $link,
            $this->glueCssSelectors($menuConfig->getSubMenuSelectors()),
            $menuName,
            $item['id']
        );
    }

    private function glueCssSelectors(array $cssSelectors): string
    {
        return \implode(' ', $cssSelectors);
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
    private function closeOpenedMenus(MenuConfiguration $menuConfig, array $items, int $currentIndex): string
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
    private function getMenuItemHref(int $mode, string $uri): string
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
    private function getMenuItemHrefTarget(string $target): string
    {
        return $target == 2 ? ' target="_blank"' : '';
    }

    /**
     * @param array $items
     * @param int   $currentIndex
     *
     * @return int
     */
    private function calculateChildParentLevelDiff(array $items, int $currentIndex): int
    {
        $diff = $items[$currentIndex]['level'];
        if (isset($items[$currentIndex + 1]['level'])) {
            $diff -= $items[$currentIndex + 1]['level'];
        }
        $diff *= 2;

        return $diff;
    }

    /**
     * @param array                                              $item
     * @param int                                                $selectedItemValue
     * @param \ACP3\Modules\ACP3\Menus\Helpers\MenuConfiguration $menuConfig
     *
     * @return string[]
     */
    private function getMenuItemSelectors(
        array $item,
        int $selectedItemValue,
        Menus\Helpers\MenuConfiguration $menuConfig
    ): array
    {
        $selectors = ['navi-' . $item['id']];

        if (!empty($selectedItemValue) &&
            $item['left_id'] <= $selectedItemValue &&
            $item['right_id'] > $selectedItemValue
        ) {
            $selectors[] = 'active';
        }

        return \array_merge($selectors, $menuConfig->getItemSelectors());
    }

    /**
     * @param string                                             $menuName
     * @param \ACP3\Modules\ACP3\Menus\Helpers\MenuConfiguration $menuConfig
     *
     * @return string
     */
    private function prepareMenuHtmlAttributes(string $menuName, MenuConfiguration $menuConfig): string
    {
        $selectors = \array_merge(['navigation-' . $menuName], $menuConfig->getSelectors());

        $attributes = ' class="' . $this->glueCssSelectors($selectors) . '"';
        $attributes .= !empty($menuConfig->getInlineStyle()) ? ' style="' . $menuConfig->getInlineStyle() . '"' : '';

        return $attributes;
    }

    /**
     * @param \ACP3\Modules\ACP3\Menus\Helpers\MenuConfiguration $menuConfig
     * @param string[]                                           $selectors
     *
     * @return string
     */
    private function prepareMenuItemHtmlAttributes(
        MenuConfiguration $menuConfig,
        array $selectors = []
    ): string
    {
        $selectors = \array_merge($selectors, $menuConfig->getLinkSelectors());

        return !empty($selectors) ? ' class="' . $this->glueCssSelectors($selectors) . '"' : '';
    }

    /**
     * @param array $params
     *
     * @return \ACP3\Modules\ACP3\Menus\Helpers\MenuConfiguration
     */
    private function createMenuConfig(array $params): MenuConfiguration
    {
        return new MenuConfiguration(
            $params['use_bootstrap'] ?? true,
            $params['class'] ?? [],
            $params['dropdownItemClass'] ?? [],
            $params['tag'] ?? 'ul',
            $params['itemTag'] ?? 'li',
            $params['dropdownWrapperTag'] ?? 'li',
            $params['classLink'] ?? [],
            $params['inlineStyles'] ?? '',
            $params['itemSelectors'] ?? [],
            $params['subMenuSelectors'] ?? []
        );
    }
}
