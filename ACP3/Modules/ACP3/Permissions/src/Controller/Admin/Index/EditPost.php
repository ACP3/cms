<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Permissions\Controller\Admin\Index;

use ACP3\Core;
use ACP3\Core\Helpers\FormAction;
use ACP3\Modules\ACP3\Permissions;

class EditPost extends Core\Controller\AbstractWidgetAction
{
    /**
     * @var \ACP3\Modules\ACP3\Permissions\Validation\RoleFormValidation
     */
    private $roleFormValidation;
    /**
     * @var Permissions\Model\AclRoleModel
     */
    private $rolesModel;
    /**
     * @var \ACP3\Core\Helpers\FormAction
     */
    private $actionHelper;
    /**
     * @var Permissions\Model\AclPermissionModel
     */
    private $aclPermissionModel;

    public function __construct(
        Core\Controller\Context\WidgetContext $context,
        FormAction $actionHelper,
        Permissions\Model\AclRoleModel $rolesModel,
        Permissions\Model\AclPermissionModel $aclPermissionModel,
        Permissions\Validation\RoleFormValidation $roleFormValidation
    ) {
        parent::__construct($context);

        $this->roleFormValidation = $roleFormValidation;
        $this->rolesModel = $rolesModel;
        $this->actionHelper = $actionHelper;
        $this->aclPermissionModel = $aclPermissionModel;
    }

    /**
     * @return array|string|\Symfony\Component\HttpFoundation\JsonResponse|\Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     *
     * @throws \Doctrine\DBAL\ConnectionException
     * @throws \Doctrine\DBAL\Exception
     */
    public function __invoke(int $id)
    {
        return $this->actionHelper->handleSaveAction(function () use ($id) {
            $formData = $this->request->getPost()->all();

            $this->roleFormValidation
                ->setRoleId($id)
                ->validate($formData);

            $formData['parent_id'] = $id === 1 ? 0 : $formData['parent_id'];

            $result = $this->rolesModel->save($formData, $id);
            $this->aclPermissionModel->updatePermissions($formData['resources'], $id);

            return $result;
        });
    }
}
