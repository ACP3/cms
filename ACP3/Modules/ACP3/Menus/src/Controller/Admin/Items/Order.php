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
    private $menuItemRepository;
    /**
     * @var \ACP3\Core\Http\RedirectResponse
     */
    private $redirectResponse;
    /**
     * @var \ACP3\Modules\ACP3\Menus\Model\MenuItemsModel
     */
    private $menuItemsModel;

    public function __construct(
        Core\Controller\Context\FrontendContext $context,
        Core\Http\RedirectResponse $redirectResponse,
        Menus\Model\Repository\MenuItemRepository $menuItemRepository,
        Menus\Model\MenuItemsModel $menuItemsModel
    ) {
        parent::__construct($context);

        $this->menuItemRepository = $menuItemRepository;
        $this->redirectResponse = $redirectResponse;
        $this->menuItemsModel = $menuItemsModel;
    }

    /**
     * @return \Symfony\Component\HttpFoundation\JsonResponse|\Symfony\Component\HttpFoundation\RedirectResponse
     *
     * @throws \Doctrine\DBAL\DBALException
     */
    public function execute(int $id, string $action)
    {
        if (($action === 'up' || $action === 'down') && $this->menuItemRepository->menuItemExists($id) === true) {
            if ($action === 'up') {
                $this->menuItemsModel->moveUp($id);
            } else {
                $this->menuItemsModel->moveDown($id);
            }

            return $this->redirectResponse->temporary('acp/menus');
        }

        throw new Core\Controller\Exception\ResultNotExistsException();
    }
}
