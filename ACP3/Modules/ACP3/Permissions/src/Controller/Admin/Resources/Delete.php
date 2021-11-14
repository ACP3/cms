<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Permissions\Controller\Admin\Resources;

use ACP3\Core;
use ACP3\Core\Helpers\FormAction;
use ACP3\Modules\ACP3\Permissions;

class Delete extends Core\Controller\AbstractWidgetAction
{
    public function __construct(
        Core\Controller\Context\WidgetContext $context,
        private FormAction                            $actionHelper,
        private Permissions\Model\AclResourceModel    $resourcesModel
    ) {
        parent::__construct($context);
    }

    public function __invoke(?string $action = null): array|\Symfony\Component\HttpFoundation\JsonResponse|\Symfony\Component\HttpFoundation\RedirectResponse
    {
        return $this->actionHelper->handleDeleteAction(
            $action,
            fn(array $items) => $this->resourcesModel->delete($items)
        );
    }
}
