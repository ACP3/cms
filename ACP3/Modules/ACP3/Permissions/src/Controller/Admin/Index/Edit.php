<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Permissions\Controller\Admin\Index;

use ACP3\Core;
use ACP3\Modules\ACP3\Permissions;

class Edit extends Core\Controller\AbstractFrontendAction
{
    /**
     * @var \ACP3\Modules\ACP3\Permissions\Validation\RoleFormValidation
     */
    private $roleFormValidation;
    /**
     * @var Permissions\Model\RolesModel
     */
    private $rolesModel;
    /**
     * @var Permissions\Model\RulesModel
     */
    private $rulesModel;
    /**
     * @var \ACP3\Modules\ACP3\Permissions\ViewProviders\AdminRoleEditViewProvider
     */
    private $adminRoleEditViewProvider;

    public function __construct(
        Core\Controller\Context\FrontendContext $context,
        Permissions\Model\RolesModel $rolesModel,
        Permissions\Model\RulesModel $rulesModel,
        Permissions\Validation\RoleFormValidation $roleFormValidation,
        Permissions\ViewProviders\AdminRoleEditViewProvider $adminRoleEditViewProvider
    ) {
        parent::__construct($context);

        $this->roleFormValidation = $roleFormValidation;
        $this->rolesModel = $rolesModel;
        $this->rulesModel = $rulesModel;
        $this->adminRoleEditViewProvider = $adminRoleEditViewProvider;
    }

    /**
     * @throws \Doctrine\DBAL\DBALException
     */
    public function execute(int $id): array
    {
        $role = $this->rolesModel->getOneById($id);

        if (!empty($role)) {
            return ($this->adminRoleEditViewProvider)($role);
        }

        throw new Core\Controller\Exception\ResultNotExistsException();
    }

    /**
     * @return array|string|\Symfony\Component\HttpFoundation\JsonResponse|\Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     *
     * @throws \Doctrine\DBAL\ConnectionException
     * @throws \Doctrine\DBAL\DBALException
     */
    public function executePost(int $id)
    {
        return $this->actionHelper->handleSaveAction(function () use ($id) {
            $formData = $this->request->getPost()->all();

            $this->roleFormValidation
                ->setRoleId($id)
                ->validate($formData);

            $formData['parent_id'] = $id === 1 ? 0 : $formData['parent_id'];

            $result = $this->rolesModel->save($formData, $id);
            $this->rulesModel->updateRules($formData['privileges'], $id);

            return $result;
        });
    }
}
