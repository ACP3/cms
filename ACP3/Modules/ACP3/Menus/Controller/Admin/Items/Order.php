<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Menus\Controller\Admin\Items;

use ACP3\Core;
use ACP3\Modules\ACP3\Menus;

class Order extends Core\Controller\AbstractFormAction
{
    /**
     * @var \ACP3\Modules\ACP3\Menus\Model\Repository\MenuItemRepository
     */
    protected $menuItemRepository;
    /**
     * @var \ACP3\Modules\ACP3\Menus\Cache
     */
    protected $menusCache;
    /**
     * @var Core\NestedSet\Operation\Sort
     */
    protected $sortOperation;

    public function __construct(
        Core\Controller\Context\FormContext $context,
        Core\NestedSet\Operation\Sort $sortOperation,
        Menus\Model\Repository\MenuItemRepository $menuItemRepository,
        Menus\Cache $menusCache
    ) {
        parent::__construct($context);

        $this->menuItemRepository = $menuItemRepository;
        $this->menusCache = $menusCache;
        $this->sortOperation = $sortOperation;
    }

    /**
     * @param int    $id
     * @param string $action
     *
     * @return \Symfony\Component\HttpFoundation\JsonResponse|\Symfony\Component\HttpFoundation\RedirectResponse
     *
     * @throws \ACP3\Core\Controller\Exception\ResultNotExistsException
     * @throws \Doctrine\DBAL\DBALException
     */
    public function execute(int $id, string $action)
    {
        if ($this->menuItemRepository->menuItemExists($id) === true) {
            $this->sortOperation->execute($id, $action);

            $this->menusCache->saveMenusCache();

            Core\Cache\Purge::doPurge($this->appPath->getCacheDir() . 'http');

            return $this->redirect()->temporary('acp/menus');
        }

        throw new Core\Controller\Exception\ResultNotExistsException();
    }
}
