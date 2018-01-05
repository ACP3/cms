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
     * @var Permissions\Model\AclResourcesModel
     */
    protected $resourcesModel;

    /**
     * Delete constructor.
     *
     * @param \ACP3\Core\Controller\Context\FrontendContext $context
     * @param Permissions\Model\AclResourcesModel $resourcesModel
     */
    public function __construct(
        Core\Controller\Context\FrontendContext $context,
        Permissions\Model\AclResourcesModel $resourcesModel
    ) {
        parent::__construct($context);

        $this->resourcesModel = $resourcesModel;
    }

    /**
     * @param string $action
     *
     * @return mixed
     * @throws \ACP3\Core\Controller\Exception\ResultNotExistsException
     */
    public function execute($action = '')
    {
        return $this->actionHelper->handleDeleteAction(
            $action,
            function (array $items) {
                return $this->resourcesModel->delete($items);
            }
        );
    }
}
