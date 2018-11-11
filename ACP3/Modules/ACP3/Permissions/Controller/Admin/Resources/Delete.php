<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Permissions\Controller\Admin\Resources;

use ACP3\Core;
use ACP3\Modules\ACP3\Permissions;

class Delete extends Core\Controller\AbstractFormAction
{
    /**
     * @var Permissions\Model\ResourcesModel
     */
    protected $resourcesModel;

    public function __construct(
        Core\Controller\Context\FormContext $context,
        Permissions\Model\ResourcesModel $resourcesModel
    ) {
        parent::__construct($context);

        $this->resourcesModel = $resourcesModel;
    }

    /**
     * @param string $action
     *
     * @return mixed
     *
     * @throws \ACP3\Core\Controller\Exception\ResultNotExistsException
     */
    public function execute(string $action = '')
    {
        return $this->actionHelper->handleDeleteAction(
            $action,
            function (array $items) {
                return $this->resourcesModel->delete($items);
            }
        );
    }
}
