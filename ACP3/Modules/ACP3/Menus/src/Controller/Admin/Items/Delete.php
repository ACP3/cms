<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Menus\Controller\Admin\Items;

use ACP3\Core;
use ACP3\Core\Helpers\FormAction;
use ACP3\Modules\ACP3\Menus;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;

class Delete extends Core\Controller\AbstractWidgetAction
{
    public function __construct(
        Core\Controller\Context\WidgetContext $context,
        private FormAction $actionHelper,
        private Menus\Model\MenuItemsModel $menuItemsModel
    ) {
        parent::__construct($context);
    }

    /**
     * @return array<string, mixed>|JsonResponse|RedirectResponse|Response
     */
    public function __invoke(?string $action = null): array|JsonResponse|RedirectResponse|Response
    {
        return $this->actionHelper->handleDeleteAction(
            $action,
            fn (array $items) => $this->menuItemsModel->delete($items),
            null,
            'acp/menus'
        );
    }
}
