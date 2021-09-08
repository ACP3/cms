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
    private $menus = [];
    /**
     * @var \ACP3\Core\Http\RequestInterface
     */
    private $request;
    /**
     * @var \ACP3\Core\Router\RouterInterface
     */
    private $router;
    /**
     * @var \ACP3\Modules\ACP3\Menus\Repository\MenuItemRepository
     */
    private $menuItemRepository;
    /**
     * @var Menus\Services\MenuServiceInterface
     */
    private $menuService;

    public function __construct(
        Core\Http\RequestInterface $request,
        Core\Router\RouterInterface $router,
        Menus\Repository\MenuItemRepository $menuItemRepository,
        Menus\Services\MenuServiceInterface $menuService
    ) {
        $this->request = $request;
        $this->router = $router;
        $this->menuItemRepository = $menuItemRepository;
        $this->menuService = $menuService;
    }

    /**
     * {@inheritdoc}
     *
     * @throws \Doctrine\DBAL\Exception
     */
    public function __invoke(array $params, \Smarty_Internal_Template $smarty): string
    {
        return $this->getMenuByKey(
            $params['block'],
            new MenuConfiguration(
                $params['use_bootstrap'] ?? true,
                $params['class'] ?? '',
                $params['dropdownItemClass'] ?? '',
                $params['tag'] ?? 'ul',
                $params['itemTag'] ?? 'li',
                $params['itemSelectors'] ?? '',
                $params['dropdownWrapperTag'] ?? 'li',
                $params['classLink'] ?? '',
                $params['inlineStyles'] ?? ''
            )
        );
    }

    /**
     * Verarbeitet die Navigationsleiste und selektiert die aktuelle Seite,
     * falls diese sich ebenfalls in der Navigationsleiste befindet.
     *
     * @throws \Doctrine\DBAL\Exception
     */
    private function getMenuByKey(
        string $menu,
        MenuConfiguration $menuConfig
    ): string {
        $cacheKey = $this->buildMenuCacheKey($menu, $menuConfig);

        return $this->menus[$cacheKey] ?? $this->generateMenu($menu, $menuConfig);
    }

    private function buildMenuCacheKey(string $menu, MenuConfiguration $menuConfig): string
    {
        return $menu . ':' . $menuConfig->__toString();
    }

    /**
     * @throws \Doctrine\DBAL\Exception
     */
    private function generateMenu(string $menu, MenuConfiguration $menuConfig): string
    {
        $items = $this->menuService->getVisibleMenuItemsByMenu($menu);

        if (\count($items) === 0) {
            return '';
        }

        $leftIdOfMatchedMenuItem = $this->selectMenuItem($menu);

        $cacheKey = $this->buildMenuCacheKey($menu, $menuConfig);

        $this->menus[$cacheKey] = '';

        foreach ($items as $i => $item) {
            $isSelected = $item['left_id'] <= $leftIdOfMatchedMenuItem && $item['right_id'] > $leftIdOfMatchedMenuItem;
            $itemSelectors = $this->getMenuItemSelector($item, $menuConfig);

            if (isset($items[$i + 1]) && $items[$i + 1]['level'] > $item['level']) {
                $this->menus[$cacheKey] .= $this->processMenuItemWithChildren(
                    $menu,
                    $menuConfig,
                    $item,
                    $itemSelectors,
                    $isSelected
                );
            } else {
                $this->menus[$cacheKey] .= $this->processMenuItemWithoutChildren(
                    $menuConfig,
                    $item,
                    $itemSelectors,
                    $isSelected
                );
                $this->menus[$cacheKey] .= $this->closeOpenedMenus(
                    $menuConfig,
                    $items,
                    $i
                );
            }
        }

        if (!empty($this->menus[$cacheKey])) {
            $this->menus[$cacheKey] = sprintf(
                '<%1$s%2$s>%3$s</%1$s>',
                $menuConfig->getTag(),
                $this->prepareMenuHtmlAttributes($menu, $menuConfig),
                $this->menus[$cacheKey]
            );
        } else {
            $this->menus[$cacheKey] = '';
        }

        return $this->menus[$cacheKey];
    }

    /**
     * Returns the left_id of the matching menu item based on the current request-URI.
     *
     * @throws \Doctrine\DBAL\Exception
     */
    private function selectMenuItem(string $menu): int
    {
        if ($this->request->getArea() !== Core\Controller\AreaEnum::AREA_ADMIN) {
            $in = [
                $this->request->getQuery(),
                $this->request->getUriWithoutPages(),
                $this->request->getFullPath(),
                $this->request->getModuleAndController(),
                $this->request->getModule(),
            ];

            return $this->menuItemRepository->getLeftIdByUris($menu, $in);
        }

        return 0;
    }

    /**
     * @param array<string, mixed> $item
     */
    private function processMenuItemWithoutChildren(MenuConfiguration $menuConfig, array $item, string $cssSelectors, bool $isSelected): string
    {
        $link = sprintf(
            '<a href="%1$s"%2$s%3$s>%4$s</a>',
            $this->getMenuItemHref($item['mode'], $item['uri']),
            $this->getMenuItemHrefTarget($item['target']),
            $this->prepareMenuItemHtmlAttributes($menuConfig, $item, $isSelected),
            $item['title']
        );

        if ($menuConfig->getItemTag() === '') {
            return $link;
        }

        return sprintf('<%1$s class="%2$s">%3$s</%1$s>', $menuConfig->getItemTag(), $cssSelectors, $link);
    }

    /**
     * @param array<string, mixed> $item
     */
    private function processMenuItemWithChildren(string $menuName, MenuConfiguration $menuConfig, array $item, string $cssSelectors, bool $isSelected): string
    {
        $attributes = $this->prepareMenuItemHtmlAttributes($menuConfig, $item, $isSelected, ['dropdown-toggle']);
        $subMenuCss = '';
        // Special styling for bootstrap enabled navigation bars
        if ($menuConfig->isUseBootstrap() === true) {
            $dropDownItemClassName = 'navigation-' . $menuName . '-subnav-' . $item['id'] . '-dropdown';
            $cssSelectors .= !empty($menuConfig->getDropdownItemSelector()) ? ' ' . $menuConfig->getDropdownItemSelector() : ' dropdown';
            $cssSelectors .= ' ' . $dropDownItemClassName;
            $attributes .= ' data-bs-toggle="dropdown" aria-expanded="false" role="button"';
            $subMenuCss = 'dropdown-menu ';
        }

        $link = sprintf(
            '<a href="%1$s"%2$s%3$s>%4$s</a>',
            $this->getMenuItemHref($item['mode'], $item['uri']),
            $this->getMenuItemHrefTarget($item['target']),
            $attributes,
            $item['title'],
        );

        return sprintf(
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
     */
    private function closeOpenedMenus(MenuConfiguration $menuConfig, array $items, int $currentIndex): string
    {
        $data = '';
        if ((isset($items[$currentIndex + 1]) && $items[$currentIndex + 1]['level'] < $items[$currentIndex]['level']) ||
            (!isset($items[$currentIndex + 1]) && (int) $items[$currentIndex]['level'] !== 0)
        ) {
            // Calculate, how many levels between the current and the next element are
            $diff = $this->calculateChildParentLevelDiff($items, $currentIndex);

            for (; $diff > 0; --$diff) {
                $data .= ($diff % 2 === 0 ? '</ul>' : '</' . $menuConfig->getDropdownWrapperTag() . '>');
            }
        }

        return $data;
    }

    private function getMenuItemHref(int $mode, string $uri): string
    {
        if ($mode === 1 || $mode === 2) {
            return $this->router->route($uri);
        }

        return $uri;
    }

    private function getMenuItemHrefTarget(int $target): string
    {
        return $target === 2 ? ' target="_blank"' : '';
    }

    private function calculateChildParentLevelDiff(array $items, int $currentIndex): int
    {
        $diff = $items[$currentIndex]['level'];
        if (isset($items[$currentIndex + 1]['level'])) {
            $diff -= $items[$currentIndex + 1]['level'];
        }
        $diff *= 2;

        return $diff;
    }

    private function getMenuItemSelector(array $item, MenuConfiguration $menuConfig): string
    {
        return implode(' ', ['navi-' . $item['id'], $menuConfig->getItemSelectors()]);
    }

    private function prepareMenuHtmlAttributes(string $menu, MenuConfiguration $menuConfig): string
    {
        $bootstrapSelector = $menuConfig->isUseBootstrap() === true ? ' navbar-nav' : '';
        $navigationSelectors = !empty($menuConfig->getSelector()) ? ' ' . $menuConfig->getSelector() : $bootstrapSelector;
        $attributes = ' class="navigation-' . $menu . $navigationSelectors . '"';
        $attributes .= !empty($menuConfig->getInlineStyle()) ? ' style="' . $menuConfig->getInlineStyle() . '"' : '';

        return $attributes;
    }

    /**
     * @param array<string, mixed> $item
     */
    private function prepareMenuItemHtmlAttributes(MenuConfiguration $menuConfig, array $item, bool $isSelected, array $additionalSelectors = []): string
    {
        if ($item['level'] > 0 && $menuConfig->isUseBootstrap()) {
            $selectors = ['dropdown-item'];
        } else {
            $selectors = array_merge([$menuConfig->getLinkSelector()], $additionalSelectors);
        }

        if ($isSelected) {
            $selectors[] = 'active';
        }

        return !empty($selectors) ? ' class="' . implode(' ', $selectors) . '"' : '';
    }
}
