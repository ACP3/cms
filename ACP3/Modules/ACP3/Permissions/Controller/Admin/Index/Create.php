<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Permissions\Controller\Admin\Index;

use ACP3\Core;
use ACP3\Modules\ACP3\Permissions;

class Create extends AbstractFormAction
{
    /**
     * @var \ACP3\Core\Helpers\FormToken
     */
    protected $formTokenHelper;
    /**
     * @var \ACP3\Modules\ACP3\Permissions\Validation\RoleFormValidation
     */
    protected $roleFormValidation;
    /**
     * @var Permissions\Model\RolesModel
     */
    protected $roleModel;
    /**
     * @var Permissions\Model\RulesModel
     */
    protected $rulesModel;

    public function __construct(
        Core\Controller\Context\FormContext $context,
        Permissions\Model\RolesModel $rolesModel,
        Permissions\Model\RulesModel $rulesModel,
        Permissions\Model\Repository\PrivilegeRepository $privilegeRepository,
        Core\Helpers\Forms $formsHelper,
        Core\Helpers\FormToken $formTokenHelper,
        Permissions\Cache $permissionsCache,
        Permissions\Validation\RoleFormValidation $roleFormValidation
    ) {
        parent::__construct($context, $formsHelper, $privilegeRepository, $permissionsCache);

        $this->formTokenHelper = $formTokenHelper;
        $this->roleFormValidation = $roleFormValidation;
        $this->roleModel = $rolesModel;
        $this->rulesModel = $rulesModel;
    }

    /**
     * @return array
     */
    public function execute()
    {
        return [
            'modules' => $this->fetchModulePermissions(0, 2),
            'parent' => $this->fetchRoles(),
            'form' => \array_merge(['name' => ''], $this->request->getPost()->all()),
            'form_token' => $this->formTokenHelper->renderFormToken(),
        ];
    }

    /**
     * @return array|string|\Symfony\Component\HttpFoundation\JsonResponse|\Symfony\Component\HttpFoundation\RedirectResponse
     *
     * @throws \Doctrine\DBAL\ConnectionException
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
