<?php
/**
 * Copyright (c) 2016 by the ACP3 Developers.
 * See the LICENCE file at the top-level module directory for licencing details.
 */

namespace ACP3\Modules\ACP3\Permissions\Controller\Admin\Resources;

use ACP3\Core;
use ACP3\Modules\ACP3\Permissions;

/**
 * Class Delete
 * @package ACP3\Modules\ACP3\Permissions\Controller\Admin\Resources
 */
class Delete extends Core\Controller\AbstractAdminAction
{
    /**
     * @var Permissions\Model\ResourcesModel
     */
    protected $resourcesModel;

    /**
     * Delete constructor.
     *
     * @param \ACP3\Core\Controller\Context\AdminContext $context
     * @param Permissions\Model\ResourcesModel $resourcesModel
     */
    public function __construct(
        Core\Controller\Context\AdminContext $context,
        Permissions\Model\ResourcesModel $resourcesModel
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
