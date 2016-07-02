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
     * @var \ACP3\Core\NestedSet\NestedSet
     */
    protected $nestedSet;
    /**
     * @var \ACP3\Core\Helpers\FormToken
     */
    protected $formTokenHelper;
    /**
     * @var \ACP3\Modules\ACP3\Permissions\Validation\RoleFormValidation
     */
    protected $roleFormValidation;

    /**
     * Create constructor.
     *
     * @param \ACP3\Core\Controller\Context\AdminContext                   $context
     * @param \ACP3\Modules\ACP3\Permissions\Model\Repository\PrivilegeRepository     $privilegeRepository
     * @param \ACP3\Modules\ACP3\Permissions\Model\Repository\RuleRepository          $ruleRepository
     * @param \ACP3\Core\NestedSet\NestedSet                                         $nestedSet
     * @param \ACP3\Core\Helpers\Forms                                     $formsHelper
     * @param \ACP3\Core\Helpers\FormToken                                 $formTokenHelper
     * @param \ACP3\Modules\ACP3\Permissions\Cache                         $permissionsCache
     * @param \ACP3\Modules\ACP3\Permissions\Validation\RoleFormValidation $roleFormValidation
     */
    public function __construct(
        Core\Controller\Context\AdminContext $context,
        Permissions\Model\Repository\PrivilegeRepository $privilegeRepository,
        Permissions\Model\Repository\RuleRepository $ruleRepository,
        Core\NestedSet\NestedSet $nestedSet,
        Core\Helpers\Forms $formsHelper,
        Core\Helpers\FormToken $formTokenHelper,
        Permissions\Cache $permissionsCache,
        Permissions\Validation\RoleFormValidation $roleFormValidation
    ) {
        parent::__construct($context, $formsHelper, $privilegeRepository, $ruleRepository, $permissionsCache);

        $this->nestedSet = $nestedSet;
        $this->formTokenHelper = $formTokenHelper;
        $this->roleFormValidation = $roleFormValidation;
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

            $insertValues = [
                'id' => '',
                'name' => $this->get('core.helpers.secure')->strEncode($formData['name']),
                'parent_id' => $formData['parent_id'],
            ];

            $roleId = $this->nestedSet->insertNode(
                (int)$formData['parent_id'],
                $insertValues,
                Permissions\Model\Repository\RoleRepository::TABLE_NAME,
                true
            );

            $this->saveRules($formData['privileges'], $roleId);

            $this->permissionsCache->saveRolesCache();

            Core\Cache\Purge::doPurge($this->appPath->getCacheDir() . 'http');

            return $roleId;
        });
    }

    /**
     * @param int $roleId
     * @param int $moduleId
     * @param int $privilegeId
     * @param int $defaultValue
     *
     * @return array
     */
    protected function generatePrivilegeCheckboxes($roleId, $moduleId, $privilegeId, $defaultValue)
    {
        $permissions = [
            0 => 'deny_access',
            1 => 'allow_access',
            2 => 'inherit_access'
        ];

        $select = [];
        foreach ($permissions as $value => $phrase) {
            if ($roleId === 1 && $value === 2) {
                continue;
            }

            $select[$value] = [
                'value' => $value,
                'selected' => $this->privilegeIsChecked($moduleId, $privilegeId, $value, $defaultValue),
                'lang' => $this->translator->t('permissions', $phrase)
            ];
        }

        return $select;
    }
}
