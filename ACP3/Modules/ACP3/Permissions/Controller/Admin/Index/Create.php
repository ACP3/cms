<?php
/**
 * Copyright (c) 2016 by the ACP3 Developers. See the LICENCE file at the top-level module directory for licencing details.
 */

namespace ACP3\Modules\ACP3\Permissions\Controller\Admin\Index;

use ACP3\Core;
use ACP3\Modules\ACP3\Permissions;

/**
 * Class Create
 * @package ACP3\Modules\ACP3\Permissions\Controller\Admin\Index
 */
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
     * Create constructor.
     *
     * @param \ACP3\Core\Controller\Context\AdminContext $context
     * @param Permissions\Model\RolesModel $rolesModel
     * @param \ACP3\Modules\ACP3\Permissions\Model\Repository\PrivilegeRepository $privilegeRepository
     * @param \ACP3\Core\Helpers\Forms $formsHelper
     * @param \ACP3\Core\Helpers\FormToken $formTokenHelper
     * @param \ACP3\Modules\ACP3\Permissions\Cache $permissionsCache
     * @param \ACP3\Modules\ACP3\Permissions\Validation\RoleFormValidation $roleFormValidation
     */
    public function __construct(
        Core\Controller\Context\AdminContext $context,
        Permissions\Model\RolesModel $rolesModel,
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
    }

    /**
     * @return array|\Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function execute()
    {
        if ($this->request->getPost()->count() !== 0) {
            return $this->executePost($this->request->getPost()->all());
        }

        return [
            'modules' => $this->fetchModulePermissions(0, 2),
            'parent' => $this->fetchRoles(),
            'form' => array_merge(['name' => ''], $this->request->getPost()->all()),
            'form_token' => $this->formTokenHelper->renderFormToken()
        ];
    }

    /**
     * @param array $formData
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     * @throws \Doctrine\DBAL\ConnectionException
     */
    protected function executePost(array $formData)
    {
        return $this->actionHelper->handleCreatePostAction(function () use ($formData) {
            $this->roleFormValidation->validate($formData);

            return $this->roleModel->saveRole($formData);
        });
    }
}
