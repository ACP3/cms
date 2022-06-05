<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Menus\Services;

use ACP3\Core\I18n\Translator;
use ACP3\Modules\ACP3\Menus\Repository\MenuItemRepository;
use ACP3\Modules\ACP3\Menus\Repository\MenuRepository;

class MenuService implements MenuServiceInterface
{
    public function __construct(private readonly Translator $translator, private readonly MenuRepository $menuRepository, private readonly MenuItemRepository $menuItemRepository)
    {
    }

    /**
     * {@inheritDoc}
     *
     * @throws \Doctrine\DBAL\Exception
     */
    public function getAllMenuItems(): array
    {
        $menuItems = $this->menuItemRepository->getAllMenuItems();
        $cMenuItems = \count($menuItems);

        if ($cMenuItems > 0) {
            $menus = $this->menuRepository->getAllMenus();

            foreach ($menuItems as $i => $menuItem) {
                foreach ($menus as $menu) {
                    if ($menuItem['block_id'] === $menu['id']) {
                        $menuItems[$i]['block_title'] = $menu['title'];
                        $menuItems[$i]['block_name'] = $menu['index_name'];
                    }
                }
            }

            $modeSearch = ['1', '2', '3'];
            $modeReplace = [
                $this->translator->t('menus', 'module'),
                $this->translator->t('menus', 'dynamic_page'),
                $this->translator->t('menus', 'hyperlink'),
            ];

            foreach ($menuItems as $i => $menu) {
                $menuItems[$i]['mode_formatted'] = str_replace($modeSearch, $modeReplace, (string) $menu['mode']);
                $menuItems[$i]['first'] = $this->isFirstItemInSet($i, $menuItems);
                $menuItems[$i]['last'] = $this->isLastItemInSet($i, $menuItems);
            }
        }

        return $menuItems;
    }

    /**
     * @param array<array<string, mixed>> $menuItems
     */
    private function isFirstItemInSet(int $index, array $menuItems): bool
    {
        if ($index > 0) {
            for ($j = $index - 1; $j >= 0; --$j) {
                if ($menuItems[$j]['parent_id'] == $menuItems[$index]['parent_id']
                    && $menuItems[$j]['block_name'] === $menuItems[$index]['block_name']
                ) {
                    return false;
                }
            }
        }

        return true;
    }

    /**
     * @param array<array<string, mixed>> $menuItems
     */
    private function isLastItemInSet(int $index, array $menuItems): bool
    {
        $cItems = \count($menuItems);
        for ($j = $index + 1; $j < $cItems; ++$j) {
            if ($menuItems[$index]['parent_id'] == $menuItems[$j]['parent_id']
                && $menuItems[$j]['block_name'] === $menuItems[$index]['block_name']
            ) {
                return false;
            }
        }

        return true;
    }

    /**
     * {@inheritDoc}
     *
     * @throws \Doctrine\DBAL\Exception
     */
    public function getVisibleMenuItemsByMenu(string $menuIdentifier): array
    {
        return $this->menuItemRepository->getVisibleMenuItemsByBlockName($menuIdentifier);
    }
}
