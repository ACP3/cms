<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Menus\Controller\Admin\Items;

use ACP3\Core\Controller\AbstractFrontendAction;
use ACP3\Core\Controller\Context\WidgetContext;
use ACP3\Core\Controller\Exception\ResultNotExistsException;
use ACP3\Core\Controller\InvokableActionInterface;
use ACP3\Core\Http\RedirectResponse;
use ACP3\Modules\ACP3\Menus\Model\MenuItemsModel;
use ACP3\Modules\ACP3\Menus\Model\Repository\MenuItemRepository;

class OrderPost extends AbstractFrontendAction implements InvokableActionInterface
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
        WidgetContext $context,
        RedirectResponse $redirectResponse,
        MenuItemRepository $menuItemRepository,
        MenuItemsModel $menuItemsModel
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
    public function __invoke(int $id, string $action)
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
