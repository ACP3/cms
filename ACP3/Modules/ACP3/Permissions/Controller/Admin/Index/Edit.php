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
     * @var \ACP3\Core\NestedSet
     */
    protected $nestedSet;
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
     * Edit constructor.
     *
     * @param \ACP3\Core\Controller\Context\AdminContext                   $context
     * @param \ACP3\Modules\ACP3\Permissions\Model\Repository\PrivilegeRepository     $privilegeRepository
     * @param \ACP3\Modules\ACP3\Permissions\Model\Repository\RuleRepository          $ruleRepository
     * @param \ACP3\Core\NestedSet                                         $nestedSet
     * @param \ACP3\Core\Helpers\Forms                                     $formsHelper
     * @param \ACP3\Core\Helpers\FormToken                                 $formTokenHelper
     * @param \ACP3\Modules\ACP3\Permissions\Model\Repository\RoleRepository          $roleRepository
     * @param \ACP3\Modules\ACP3\Permissions\Cache                         $permissionsCache
     * @param \ACP3\Modules\ACP3\Permissions\Validation\RoleFormValidation $roleFormValidation
     */
    public function __construct(
        Core\Controller\Context\AdminContext $context,
        Permissions\Model\Repository\PrivilegeRepository $privilegeRepository,
        Permissions\Model\Repository\RuleRepository $ruleRepository,
        Core\NestedSet $nestedSet,
        Core\Helpers\Forms $formsHelper,
        Core\Helpers\FormToken $formTokenHelper,
        Permissions\Model\Repository\RoleRepository $roleRepository,
        Permissions\Cache $permissionsCache,
        Permissions\Validation\RoleFormValidation $roleFormValidation
    ) {
        parent::__construct($context, $formsHelper, $privilegeRepository, $ruleRepository, $permissionsCache);

        $this->nestedSet = $nestedSet;
        $this->formTokenHelper = $formTokenHelper;
        $this->roleRepository = $roleRepository;
        $this->roleFormValidation = $roleFormValidation;
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

            if ($id != 1) {
                $this->view->assign(
                    'parent',
                    $this->fetchRoles($role['parent_id'], $role['left_id'], $role['right_id'])
                );
            }

            return [
                'modules' => $this->fetchModulePermissions($id),
                'form' => array_merge($role, $this->request->getPost()->all()),
                'form_token' => $this->formTokenHelper->renderFormToken()
            ];
        }

        throw new Core\Controller\Exception\ResultNotExistsException();
    }

    /**
     * @param array $formData
     * @param int   $id
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     * @throws \Doctrine\DBAL\ConnectionException
     */
    protected function executePost(array $formData, $id)
    {
        return $this->actionHelper->handleEditPostAction(function () use ($formData, $id) {
            $this->roleFormValidation
                ->setRoleId($id)
                ->validate($formData);

            $updateValues = [
                'name' => $this->get('core.helpers.secure')->strEncode($formData['name']),
                'parent_id' => $id === 1 ? 0 : $formData['parent_id'],
            ];

            $bool = $this->nestedSet->editNode(
                $id,
                $id === 1 ? '' : (int)$formData['parent_id'],
                0,
                $updateValues,
                Permissions\Model\Repository\RoleRepository::TABLE_NAME
            );

            // Bestehende Berechtigungen löschen, da in der Zwischenzeit neue hinzugekommen sein könnten
            $this->ruleRepository->delete($id, 'role_id');
            $this->saveRules($formData['privileges'], $id);

            $this->permissionsCache->getCacheDriver()->deleteAll();

            Core\Cache\Purge::doPurge($this->appPath->getCacheDir() . 'http');

            return $bool;
        });
    }
}
