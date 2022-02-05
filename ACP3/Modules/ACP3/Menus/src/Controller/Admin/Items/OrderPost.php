<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Menus\Controller\Admin\Items;

use ACP3\Core\Controller\AbstractWidgetAction;
use ACP3\Core\Controller\Context\Context;
use ACP3\Core\Controller\Exception\ResultNotExistsException;
use ACP3\Core\Http\RedirectResponse;
use ACP3\Modules\ACP3\Menus\Model\MenuItemsModel;
use ACP3\Modules\ACP3\Menus\Repository\MenuItemRepository;

class OrderPost extends AbstractWidgetAction
{
    public function __construct(
        Context $context,
        private RedirectResponse $redirectResponse,
        private MenuItemRepository $menuItemRepository,
        private MenuItemsModel $menuItemsModel
    ) {
        parent::__construct($context);
    }

    /**
     * @throws \Doctrine\DBAL\Exception
     */
    public function __invoke(int $id, string $action): \Symfony\Component\HttpFoundation\JsonResponse|\Symfony\Component\HttpFoundation\RedirectResponse
    {
        if (($action === 'up' || $action === 'down') && $this->menuItemRepository->menuItemExists($id) === true) {
            if ($action === 'up') {
                $this->menuItemsModel->moveUp($id);
            } else {
                $this->menuItemsModel->moveDown($id);
            }

            return $this->redirectResponse->temporary('acp/menus');
        }

        throw new ResultNotExistsException();
    }
}
