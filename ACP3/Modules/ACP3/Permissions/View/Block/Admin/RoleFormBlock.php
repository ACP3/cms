<?php
/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENCE file at the top-level module directory for licencing details.
 */

namespace ACP3\Modules\ACP3\Permissions\View\Block\Admin;

use ACP3\Core\ACL\ACLInterface;
use ACP3\Core\Modules;
use ACP3\Core\View\Block\AbstractFormBlock;
use ACP3\Core\View\Block\Context\FormBlockContext;
use ACP3\Modules\ACP3\Permissions\Cache\PermissionsCacheStorage;
use ACP3\Modules\ACP3\Permissions\Model\Repository\AclPrivilegesRepository;

class RoleFormBlock extends AbstractFormBlock
{
    /**
     * @var ACLInterface
     */
    private $acl;
    /**
     * @var Modules
     */
    private $modules;
    /**
     * @var AclPrivilegesRepository
     */
    private $privilegeRepository;
    /**
     * @var PermissionsCacheStorage
     */
    private $permissionsCache;

    /**
     * RoleFormBlock constructor.
     * @param FormBlockContext $context
     * @param ACLInterface $acl
     * @param Modules $modules
     * @param AclPrivilegesRepository $privilegeRepository
     * @param PermissionsCacheStorage $permissionsCache
     */
    public function __construct(
        FormBlockContext $context,
        ACLInterface $acl,
        Modules $modules,
        AclPrivilegesRepository $privilegeRepository,
        PermissionsCacheStorage $permissionsCache
    ) {
        parent::__construct($context);

        $this->acl = $acl;
        $this->modules = $modules;
        $this->privilegeRepository = $privilegeRepository;
        $this->permissionsCache = $permissionsCache;
    }

    /**
     * @inheritdoc
     */
    public function render()
    {
        $role = $this->getData();

        $this->title->setPageTitlePrefix($role['name']);

        return [
            'modules' => $this->fetchModulePermissions($role['id']),
            'parent' => $role['id'] != 1
                ? $this->fetchRoles($role['parent_id'], $role['left_id'], $role['right_id'])
                : [],
            'form' => array_merge($role, $this->getRequestData()),
            'form_token' => $this->formToken->renderFormToken()
        ];
    }

    /**
     * @param int $roleParentId
     * @param int $roleLeftId
     * @param int $roleRightId
     *
     * @return array
     */
    private function fetchRoles(int $roleParentId = 0, int $roleLeftId = 0, int $roleRightId = 0): array
    {
        $roles = $this->acl->getAllRoles();
        $cRoles = count($roles);
        for ($i = 0; $i < $cRoles; ++$i) {
            if ($roles[$i]['left_id'] >= $roleLeftId && $roles[$i]['right_id'] <= $roleRightId) {
                unset($roles[$i]);
            } else {
                $roles[$i]['selected'] = $this->forms->selectEntry('roles', $roles[$i]['id'], $roleParentId);
                $roles[$i]['name'] = str_repeat('&nbsp;&nbsp;', $roles[$i]['level']) . $roles[$i]['name'];
            }
        }
        return $roles;
    }

    /**
     * @param int $roleId
     * @param int $defaultValue
     *
     * @return array
     */
    private function fetchModulePermissions(int $roleId, int $defaultValue = 0): array
    {
        $rules = $this->permissionsCache->getRulesCache([$roleId]);
        $modules = $this->modules->getActiveModules();
        $privileges = $this->privilegeRepository->getAllPrivileges();
        $cPrivileges = count($privileges);

        foreach ($modules as $name => $moduleInfo) {
            $moduleDir = strtolower($moduleInfo['dir']);
            for ($j = 0; $j < $cPrivileges; ++$j) {
                $privileges[$j]['select'] = $this->generatePrivilegeCheckboxes(
                    $roleId,
                    $moduleInfo['id'],
                    $privileges[$j]['id'],
                    isset($rules[$moduleDir][$privileges[$j]['key']]['permission']) ? (int)$rules[$moduleDir][$privileges[$j]['key']]['permission'] : $defaultValue
                );
                if ($roleId !== 0) {
                    $privileges[$j]['calculated'] = $this->calculatePermission(
                        $rules,
                        $moduleDir,
                        $privileges[$j]['key']
                    );
                }
            }
            $modules[$name]['privileges'] = $privileges;
        }
        return $modules;
    }

    /**
     * @param int $roleId
     * @param int $moduleId
     * @param int $privilegeId
     * @param int $defaultValue
     *
     * @return array
     */
    private function generatePrivilegeCheckboxes(int $roleId, int $moduleId, int $privilegeId, int $defaultValue): array
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

    /**
     * @param int $moduleId
     * @param int $privilegeId
     * @param int $value
     * @param null|int $defaultValue
     *
     * @return string
     */
    private function privilegeIsChecked(int $moduleId, int $privilegeId, int $value = 0, $defaultValue = null): string
    {
        $requestData = $this->getRequestData();
        if (count($requestData) == 0 && $defaultValue === $value ||
            count($requestData) !== 0 && (int)$requestData['privileges'][$moduleId][$privilegeId] === $value
        ) {
            return ' checked="checked"';
        }

        return '';
    }

    /**
     * @param array $rules
     * @param string $moduleDir
     * @param string $key
     *
     * @return string
     */
    private function calculatePermission(array $rules, string $moduleDir, string $key): string
    {
        return sprintf(
            $this->translator->t('permissions', 'calculated_permission'),
            $this->translator->t(
                'permissions',
                isset($rules[$moduleDir][$key]) && $rules[$moduleDir][$key]['access'] === true
                    ? 'allow_access'
                    : 'deny_access'
            )
        );
    }

    /**
     * @inheritdoc
     */
    public function getDefaultData(): array
    {
        return [
            'id' => 0,
            'name' => '',
            'parent_id' => 0,
            'left_id' => 0,
            'right_id' => 0
        ];
    }
}
