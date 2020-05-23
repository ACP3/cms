<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Permissions\Controller\Admin\Resources;

use ACP3\Core;
use ACP3\Modules\ACP3\Permissions;

class Delete extends Core\Controller\AbstractFrontendAction
{
    /**
     * @var Permissions\Model\ResourcesModel
     */
    protected $resourcesModel;

    /**
     * Delete constructor.
     *
     * @param \ACP3\Core\Controller\Context\FrontendContext $context
     * @param Permissions\Model\ResourcesModel              $resourcesModel
     */
    public function __construct(
        Core\Controller\Context\FrontendContext $context,
        Permissions\Model\ResourcesModel $resourcesModel
    ) {
        parent::__construct($context);

        $this->resourcesModel = $resourcesModel;
    }

    /**
     * @param string|null $action
     *
     * @return array|\Symfony\Component\HttpFoundation\JsonResponse|\Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function execute(?string $action = null)
    {
        return $this->actionHelper->handleDeleteAction(
            $action,
            function (array $items) {
                return $this->resourcesModel->delete($items);
            }
        );
    }
}
