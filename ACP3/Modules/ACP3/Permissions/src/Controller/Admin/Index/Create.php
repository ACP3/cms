<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Permissions\Controller\Admin\Index;

use ACP3\Core;
use ACP3\Core\Modules\Helper\Action;
use ACP3\Modules\ACP3\Permissions;

class Create extends Core\Controller\AbstractFrontendAction
{
    /**
     * @var \ACP3\Modules\ACP3\Permissions\Validation\RoleFormValidation
     */
    private $roleFormValidation;
    /**
     * @var Permissions\Model\RolesModel
     */
    private $roleModel;
    /**
     * @var Permissions\Model\RulesModel
     */
    private $rulesModel;
    /**
     * @var \ACP3\Modules\ACP3\Permissions\ViewProviders\AdminRoleEditViewProvider
     */
    private $adminRoleEditViewProvider;
    /**
     * @var \ACP3\Core\Modules\Helper\Action
     */
    private $actionHelper;

    public function __construct(
        Core\Controller\Context\FrontendContext $context,
        Action $actionHelper,
        Permissions\Model\RolesModel $rolesModel,
        Permissions\Model\RulesModel $rulesModel,
        Permissions\Validation\RoleFormValidation $roleFormValidation,
        Permissions\ViewProviders\AdminRoleEditViewProvider $adminRoleEditViewProvider
    ) {
        parent::__construct($context);

        $this->roleFormValidation = $roleFormValidation;
        $this->roleModel = $rolesModel;
        $this->rulesModel = $rulesModel;
        $this->adminRoleEditViewProvider = $adminRoleEditViewProvider;
        $this->actionHelper = $actionHelper;
    }

    /**
     * @throws \Doctrine\DBAL\DBALException
     */
    public function execute(): array
    {
        $defaults = [
            'id' => 0,
            'name' => '',
            'parent_id' => 0,
            'left_id' => 0,
            'right_id' => 0,
        ];

        return ($this->adminRoleEditViewProvider)($defaults);
    }

    /**
     * @return array|string|\Symfony\Component\HttpFoundation\JsonResponse|\Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     *
     * @throws \Doctrine\DBAL\ConnectionException
     * @throws \Doctrine\DBAL\DBALException
     */
    public function executePost()
    {
        return $this->actionHelper->handleSaveAction(function () {
            $formData = $this->request->getPost()->all();

            $this->roleFormValidation->validate($formData);

            $roleId = $this->roleModel->save($formData);
            $this->rulesModel->updateRules($formData['privileges'], $roleId);

            return $roleId;
        });
    }
}
