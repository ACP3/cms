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
    /**
     * @var Permissions\Model\AclResourceModel
     */
    private $resourcesModel;
    /**
     * @var \ACP3\Core\Helpers\FormAction
     */
    private $actionHelper;

    public function __construct(
        Core\Controller\Context\WidgetContext $context,
        FormAction                            $actionHelper,
        Permissions\Model\AclResourceModel    $resourcesModel
    ) {
        parent::__construct($context);

        $this->resourcesModel = $resourcesModel;
        $this->actionHelper = $actionHelper;
    }

    /**
     * @param string|null $action
     *
     * @return array|\Symfony\Component\HttpFoundation\JsonResponse|\Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function __invoke(?string $action = null)
    {
        return $this->actionHelper->handleDeleteAction(
            $action,
            function (array $items) {
                return $this->resourcesModel->delete($items);
            }
        );
    }
}
