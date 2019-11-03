<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Permissions\Controller\Admin\Index;

use ACP3\Core;
use ACP3\Modules\ACP3\Permissions;

class Edit extends AbstractFormAction
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
    protected $rolesModel;
    /**
     * @var Permissions\Model\RulesModel
     */
    protected $rulesModel;

    /**
     * Edit constructor.
     *
     * @param \ACP3\Core\Controller\Context\FrontendContext                       $context
     * @param \ACP3\Modules\ACP3\Permissions\Model\Repository\PrivilegeRepository $privilegeRepository
     * @param \ACP3\Core\Helpers\Forms                                            $formsHelper
     * @param \ACP3\Core\Helpers\FormToken                                        $formTokenHelper
     * @param \ACP3\Modules\ACP3\Permissions\Cache                                $permissionsCache
     * @param \ACP3\Modules\ACP3\Permissions\Validation\RoleFormValidation        $roleFormValidation
     */
    public function __construct(
        Core\Controller\Context\FrontendContext $context,
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
        $this->rolesModel = $rolesModel;
        $this->rulesModel = $rulesModel;
    }

    /**
     * @param int $id
     *
     * @return array
     *
     * @throws \ACP3\Core\Controller\Exception\ResultNotExistsException
     */
    public function execute($id)
    {
        $role = $this->rolesModel->getOneById($id);

        if (!empty($role)) {
            $this->title->setPageTitlePrefix($role['name']);

            return [
                'parent' => $id != 1
                    ? $this->fetchRoles($role['parent_id'], $role['left_id'], $role['right_id'])
                    : [],
                'modules' => $this->fetchModulePermissions($id),
                'form' => \array_merge($role, $this->request->getPost()->all()),
                'form_token' => $this->formTokenHelper->renderFormToken(),
            ];
        }

        throw new Core\Controller\Exception\ResultNotExistsException();
    }

    /**
     * @param int $id
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     *
     * @throws \Doctrine\DBAL\ConnectionException
     */
    public function executePost($id)
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
