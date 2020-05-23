<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Permissions\Controller\Admin\Index;

use ACP3\Core;
use ACP3\Core\Controller\AbstractFrontendAction;
use ACP3\Modules\ACP3\Permissions;

abstract class AbstractFormAction extends AbstractFrontendAction
{
    /**
     * @var \ACP3\Core\Helpers\Forms
     */
    protected $formsHelper;
    /**
     * @var \ACP3\Modules\ACP3\Permissions\Model\Repository\PrivilegeRepository
     */
    protected $privilegeRepository;
    /**
     * @var \ACP3\Modules\ACP3\Permissions\Cache
     */
    protected $permissionsCache;
    /**
     * @var \ACP3\Core\ACL
     */
    private $acl;
    /**
     * @var \ACP3\Core\Modules
     */
    private $modules;

    public function __construct(
        Core\Controller\Context\FrontendContext $context,
        Core\ACL $acl,
        Core\Modules $modules,
        Core\Helpers\Forms $formsHelper,
        Permissions\Model\Repository\PrivilegeRepository $privilegeRepository,
        Permissions\Cache $permissionsCache
    ) {
        parent::__construct($context);

        $this->formsHelper = $formsHelper;
        $this->privilegeRepository = $privilegeRepository;
        $this->permissionsCache = $permissionsCache;
        $this->acl = $acl;
        $this->modules = $modules;
    }

    protected function generatePrivilegeCheckboxes(int $roleId, int $moduleId, int $privilegeId, int $defaultValue): array
    {
        $permissions = [
            0 => 'deny_access',
            1 => 'allow_access',
            2 => 'inherit_access',
        ];

        $select = [];
        foreach ($permissions as $value => $phrase) {
            if ($roleId === 1 && $value === 2) {
                continue;
            }

            $select[$value] = [
                'value' => $value,
                'selected' => $this->privilegeIsChecked($moduleId, $privilegeId, $value, $defaultValue),
                'lang' => $this->translator->t('permissions', $phrase),
            ];
        }

        return $select;
    }

    protected function privilegeIsChecked(int $moduleId, int $privilegeId, int $value = 0, ?int $defaultValue = null): string
    {
        if (($this->request->getPost()->count() === 0 && $defaultValue === $value) ||
            ($this->request->getPost()->count() !== 0 && (int) $this->request->getPost()->get('privileges')[$moduleId][$privilegeId] === $value)
        ) {
            return ' checked="checked"';
        }

        return '';
    }

    protected function calculatePermission(array $rules, string $moduleDir, string $key): string
    {
        return \sprintf(
            $this->translator->t('permissions', 'calculated_permission'),
            $this->translator->t(
                'permissions',
                isset($rules[$moduleDir][$key]) && $rules[$moduleDir][$key]['access'] === true ? 'allow_access' : 'deny_access'
            )
        );
    }

    protected function fetchRoles(int $roleParentId = 0, int $roleLeftId = 0, int $roleRightId = 0): array
    {
        $roles = $this->acl->getAllRoles();
        foreach ($roles as $i => $role) {
            if ($role['left_id'] >= $roleLeftId && $role['right_id'] <= $roleRightId) {
                unset($roles[$i]);
            } else {
                $roles[$i]['selected'] = $this->formsHelper->selectEntry('roles', $role['id'], $roleParentId);
                $roles[$i]['name'] = \str_repeat('&nbsp;&nbsp;', $role['level']) . $role['name'];
            }
        }

        return $roles;
    }

    /**
     * @throws \Doctrine\DBAL\DBALException
     */
    protected function fetchModulePermissions(int $roleId, int $defaultValue = 0): array
    {
        $rules = $this->permissionsCache->getRulesCache([$roleId]);
        $modules = $this->modules->getActiveModules();
        $privileges = $this->privilegeRepository->getAllPrivileges();

        foreach ($modules as $name => $moduleInfo) {
            foreach ($privileges as $j => $privilege) {
                $privileges[$j]['select'] = $this->generatePrivilegeCheckboxes(
                    $roleId,
                    $moduleInfo['id'],
                    $privilege['id'],
                    isset($rules[$moduleInfo['name']][$privilege['key']]['permission']) ? (int) $rules[$moduleInfo['name']][$privilege['key']]['permission'] : $defaultValue
                );
                if ($roleId !== 0) {
                    $privileges[$j]['calculated'] = $this->calculatePermission($rules, $moduleInfo['name'], $privilege['key']);
                }
            }
            $modules[$name]['privileges'] = $privileges;
        }

        return $modules;
    }
}
