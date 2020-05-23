<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Menus\Controller\Admin\Items;

use ACP3\Core;
use ACP3\Modules\ACP3\Menus;

class Order extends Core\Controller\AbstractFrontendAction
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
    /**
     * @var \ACP3\Core\Http\RedirectResponse
     */
    private $redirectResponse;

    public function __construct(
        Core\Controller\Context\FrontendContext $context,
        Core\Http\RedirectResponse $redirectResponse,
        Core\NestedSet\Operation\Sort $sortOperation,
        Menus\Model\Repository\MenuItemRepository $menuItemRepository,
        Menus\Cache $menusCache
    ) {
        parent::__construct($context);

        $this->menuItemRepository = $menuItemRepository;
        $this->menusCache = $menusCache;
        $this->sortOperation = $sortOperation;
        $this->redirectResponse = $redirectResponse;
    }

    /**
     * @param int    $id
     * @param string $action
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     *
     * @throws \ACP3\Core\Controller\Exception\ResultNotExistsException
     */
    public function execute($id, $action)
    {
        if ($this->menuItemRepository->menuItemExists($id) === true) {
            $this->sortOperation->execute($id, $action);

            $this->menusCache->saveMenusCache();

            Core\Cache\Purge::doPurge($this->appPath->getCacheDir() . 'http');

            return $this->redirectResponse->temporary('acp/menus');
        }

        throw new Core\Controller\Exception\ResultNotExistsException();
    }
}
