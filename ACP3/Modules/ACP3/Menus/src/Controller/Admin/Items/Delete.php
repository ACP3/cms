<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Menus\Controller\Admin\Items;

use ACP3\Core;
use ACP3\Core\Helpers\FormAction;
use ACP3\Modules\ACP3\Menus;

class Delete extends Core\Controller\AbstractWidgetAction
{
    /**
     * @var Menus\Model\MenuItemsModel
     */
    private $menuItemsModel;
    /**
     * @var \ACP3\Core\Helpers\FormAction
     */
    private $actionHelper;

    public function __construct(
        Core\Controller\Context\WidgetContext $context,
        FormAction $actionHelper,
        Menus\Model\MenuItemsModel $menuItemsModel
    ) {
        parent::__construct($context);

        $this->menuItemsModel = $menuItemsModel;
        $this->actionHelper = $actionHelper;
    }

    /**
     * @return array|\Symfony\Component\HttpFoundation\JsonResponse|\Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function __invoke(?string $action = null)
    {
        return $this->actionHelper->handleDeleteAction(
            $action,
            function (array $items) {
                return $this->menuItemsModel->delete($items);
            },
            null,
            'acp/menus'
        );
    }
}
