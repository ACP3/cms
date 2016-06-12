<?php
/**
 * Copyright (c) 2016 by the ACP3 Developers.
 * See the LICENCE file at the top-level module directory for licencing details.
 */

namespace ACP3\Modules\ACP3\Permissions\Controller\Admin\Index;

use ACP3\Core;
use ACP3\Core\Controller\AdminAction;
use ACP3\Modules\ACP3\Permissions;

/**
 * Class AbstractFormAction
 * @package ACP3\Modules\ACP3\Permissions\Controller\Admin\Index
 */
abstract class AbstractFormAction extends AdminAction
{
    /**
     * @var \ACP3\Core\Helpers\Forms
     */
    protected $formsHelper;
    /**
     * @var \ACP3\Modules\ACP3\Permissions\Model\PrivilegeRepository
     */
    protected $privilegeRepository;
    /**
     * @var \ACP3\Modules\ACP3\Permissions\Model\RuleRepository
     */
    protected $ruleRepository;
    /**
     * @var \ACP3\Modules\ACP3\Permissions\Cache
     */
    protected $permissionsCache;

    /**
     * AbstractFormAction constructor.
     *
     * @param \ACP3\Core\Controller\Context\AdminContext               $context
     * @param \ACP3\Core\Helpers\Forms                                 $formsHelper
     * @param \ACP3\Modules\ACP3\Permissions\Model\PrivilegeRepository $privilegeRepository
     * @param \ACP3\Modules\ACP3\Permissions\Model\RuleRepository      $ruleRepository
     * @param \ACP3\Modules\ACP3\Permissions\Cache                     $permissionsCache
     */
    public function __construct(
        Core\Controller\Context\AdminContext $context,
        Core\Helpers\Forms $formsHelper,
        Permissions\Model\PrivilegeRepository $privilegeRepository,
        Permissions\Model\RuleRepository $ruleRepository,
        Permissions\Cache $permissionsCache
    ) {
        parent::__construct($context);

        $this->formsHelper = $formsHelper;
        $this->privilegeRepository = $privilegeRepository;
        $this->ruleRepository = $ruleRepository;
        $this->permissionsCache = $permissionsCache;
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

    /**
     * @param int      $moduleId
     * @param int      $privilegeId
     * @param int      $value
     * @param null|int $defaultValue
     *
     * @return string
     */
    protected function privilegeIsChecked($moduleId, $privilegeId, $value = 0, $defaultValue = null)
    {
        if (count($this->request->getPost()->all()) == 0 && $defaultValue === $value ||
            $this->request->getPost()->count() !== 0 && (int)$this->request->getPost()->get('privileges')[$moduleId][$privilegeId] === $value
        ) {
            return ' checked="checked"';
        }

        return '';
    }

    /**
     * @param array $privileges
     * @param int   $roleId
     */
    protected function saveRules(array $privileges, $roleId)
    {
        foreach ($privileges as $moduleId => $modulePrivileges) {
            foreach ($modulePrivileges as $privilegeId => $permission) {
                $ruleInsertValues = [
                    'id' => '',
                    'role_id' => $roleId,
                    'module_id' => $moduleId,
                    'privilege_id' => $privilegeId,
                    'permission' => $permission
                ];
                $this->ruleRepository->insert($ruleInsertValues);
            }
        }
    }

    /**
     * @param array  $rules
     * @param string $moduleDir
     * @param string $key
     *
     * @return string
     */
    protected function calculatePermission(array $rules, $moduleDir, $key)
    {
        return sprintf(
            $this->translator->t('permissions', 'calculated_permission'),
            $this->translator->t('permissions',
                isset($rules[$moduleDir][$key]) && $rules[$moduleDir][$key]['access'] === true ? 'allow_access' : 'deny_access')
        );
    }

    /**
     * @param int $roleParentId
     * @param int $roleLeftId
     * @param int $roleRightId
     *
     * @return array
     */
    protected function fetchRoles($roleParentId = 0, $roleLeftId = 0, $roleRightId = 0)
    {
        $roles = $this->acl->getAllRoles();
        $cRoles = count($roles);
        for ($i = 0; $i < $cRoles; ++$i) {
            if ($roles[$i]['left_id'] >= $roleLeftId && $roles[$i]['right_id'] <= $roleRightId) {
                unset($roles[$i]);
            } else {
                $roles[$i]['selected'] = $this->formsHelper->selectEntry('roles', $roles[$i]['id'], $roleParentId);
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
    protected function fetchModulePermissions($roleId, $defaultValue = 0)
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
                    $privileges[$j]['calculated'] = $this->calculatePermission($rules, $moduleDir, $privileges[$j]['key']);
                }
            }
            $modules[$name]['privileges'] = $privileges;
        }
        return $modules;
    }
}
