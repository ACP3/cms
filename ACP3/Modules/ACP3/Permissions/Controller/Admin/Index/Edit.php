<?php
/**
 * Copyright (c) 2016 by the ACP3 Developers. See the LICENCE file at the top-level module directory for licencing details.
 */

namespace ACP3\Modules\ACP3\Permissions\Controller\Admin\Index;

use ACP3\Core;
use ACP3\Modules\ACP3\Permissions;

/**
 * Class Edit
 * @package ACP3\Modules\ACP3\Permissions\Controller\Admin\Index
 */
class Edit extends AbstractFormAction
{
    /**
     * @var \ACP3\Core\Helpers\FormToken
     */
    protected $formTokenHelper;
    /**
     * @var \ACP3\Modules\ACP3\Permissions\Model\Repository\RoleRepository
     */
    protected $roleRepository;
    /**
     * @var \ACP3\Modules\ACP3\Permissions\Validation\RoleFormValidation
     */
    protected $roleFormValidation;
    /**
     * @var Permissions\Model\RoleModel
     */
    protected $roleModel;

    /**
     * Edit constructor.
     *
     * @param \ACP3\Core\Controller\Context\AdminContext $context
     * @param Permissions\Model\RoleModel $roleModel
     * @param \ACP3\Modules\ACP3\Permissions\Model\Repository\PrivilegeRepository $privilegeRepository
     * @param \ACP3\Modules\ACP3\Permissions\Model\Repository\RuleRepository $ruleRepository
     * @param \ACP3\Core\Helpers\Forms $formsHelper
     * @param \ACP3\Core\Helpers\FormToken $formTokenHelper
     * @param \ACP3\Modules\ACP3\Permissions\Model\Repository\RoleRepository $roleRepository
     * @param \ACP3\Modules\ACP3\Permissions\Cache $permissionsCache
     * @param \ACP3\Modules\ACP3\Permissions\Validation\RoleFormValidation $roleFormValidation
     */
    public function __construct(
        Core\Controller\Context\AdminContext $context,
        Permissions\Model\RoleModel $roleModel,
        Permissions\Model\Repository\PrivilegeRepository $privilegeRepository,
        Permissions\Model\Repository\RuleRepository $ruleRepository,
        Core\Helpers\Forms $formsHelper,
        Core\Helpers\FormToken $formTokenHelper,
        Permissions\Model\Repository\RoleRepository $roleRepository,
        Permissions\Cache $permissionsCache,
        Permissions\Validation\RoleFormValidation $roleFormValidation
    ) {
        parent::__construct($context, $formsHelper, $privilegeRepository, $ruleRepository, $permissionsCache);

        $this->formTokenHelper = $formTokenHelper;
        $this->roleRepository = $roleRepository;
        $this->roleFormValidation = $roleFormValidation;
        $this->roleModel = $roleModel;
    }

    /**
     * @param int $id
     *
     * @return array|\Symfony\Component\HttpFoundation\RedirectResponse
     * @throws \ACP3\Core\Controller\Exception\ResultNotExistsException
     */
    public function execute($id)
    {
        $role = $this->roleRepository->getRoleById($id);

        if (!empty($role)) {
            $this->title->setPageTitlePostfix($role['name']);

            if ($this->request->getPost()->count() !== 0) {
                return $this->executePost($this->request->getPost()->all(), $id);
            }

            return [
                'parent' => $id != 1
                    ? $this->fetchRoles($role['parent_id'], $role['left_id'], $role['right_id'])
                    : [],
                'modules' => $this->fetchModulePermissions($id),
                'form' => array_merge($role, $this->request->getPost()->all()),
                'form_token' => $this->formTokenHelper->renderFormToken()
            ];
        }

        throw new Core\Controller\Exception\ResultNotExistsException();
    }

    /**
     * @param array $formData
     * @param int   $roleId
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     * @throws \Doctrine\DBAL\ConnectionException
     */
    protected function executePost(array $formData, $roleId)
    {
        return $this->actionHelper->handleEditPostAction(function () use ($formData, $roleId) {
            $this->roleFormValidation
                ->setRoleId($roleId)
                ->validate($formData);

            $formData['parent_id'] = $roleId === 1 ? 0 : $formData['parent_id'];
            $bool = $this->roleModel->saveRole($formData, $roleId);

            // Bestehende Berechtigungen löschen, da in der Zwischenzeit neue hinzugekommen sein könnten
            $this->ruleRepository->delete($roleId, 'role_id');
            $this->saveRules($formData['privileges'], $roleId);

            $this->permissionsCache->getCacheDriver()->deleteAll();

            return $bool;
        });
    }
}
