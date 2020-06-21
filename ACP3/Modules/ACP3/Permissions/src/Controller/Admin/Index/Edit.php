<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Permissions\Controller\Admin\Index;

use ACP3\Core;
use ACP3\Modules\ACP3\Permissions;

class Edit extends Core\Controller\AbstractFrontendAction implements Core\Controller\InvokableActionInterface
{
    /**
     * @var Permissions\Model\RolesModel
     */
    private $rolesModel;
    /**
     * @var \ACP3\Modules\ACP3\Permissions\ViewProviders\AdminRoleEditViewProvider
     */
    private $adminRoleEditViewProvider;

    public function __construct(
        Core\Controller\Context\FrontendContext $context,
        Permissions\Model\RolesModel $rolesModel,
        Permissions\ViewProviders\AdminRoleEditViewProvider $adminRoleEditViewProvider
    ) {
        parent::__construct($context);

        $this->rolesModel = $rolesModel;
        $this->adminRoleEditViewProvider = $adminRoleEditViewProvider;
    }

    /**
     * @throws \Doctrine\DBAL\DBALException
     */
    public function __invoke(int $id): array
    {
        $role = $this->rolesModel->getOneById($id);

        if (!empty($role)) {
            return ($this->adminRoleEditViewProvider)($role);
        }

        throw new Core\Controller\Exception\ResultNotExistsException();
    }
}
