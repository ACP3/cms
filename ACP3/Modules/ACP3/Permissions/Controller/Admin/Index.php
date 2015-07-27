<?php

namespace ACP3\Modules\ACP3\Permissions\Controller\Admin;

use ACP3\Core;
use ACP3\Modules\ACP3\Permissions;

/**
 * Class Index
 * @package ACP3\Modules\ACP3\Permissions\Controller\Admin
 */
class Index extends Core\Modules\AdminController
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
     * @var \ACP3\Modules\ACP3\Permissions\Model
     */
    protected $permissionsModel;
    /**
     * @var \ACP3\Modules\ACP3\Permissions\Cache
     */
    protected $permissionsCache;
    /**
     * @var \ACP3\Modules\ACP3\Permissions\Validator
     */
    protected $permissionsValidator;

    /**
     * @param \ACP3\Core\Modules\Controller\AdminContext $context
     * @param \ACP3\Core\NestedSet                       $nestedSet
     * @param \ACP3\Core\Helpers\FormToken               $formTokenHelper
     * @param \ACP3\Modules\ACP3\Permissions\Model       $permissionsModel
     * @param \ACP3\Modules\ACP3\Permissions\Cache       $permissionsCache
     * @param \ACP3\Modules\ACP3\Permissions\Validator   $permissionsValidator
     */
    public function __construct(
        Core\Modules\Controller\AdminContext $context,
        Core\NestedSet $nestedSet,
        Core\Helpers\FormToken $formTokenHelper,
        Permissions\Model $permissionsModel,
        Permissions\Cache $permissionsCache,
        Permissions\Validator $permissionsValidator)
    {
        parent::__construct($context);

        $this->nestedSet = $nestedSet;
        $this->formTokenHelper = $formTokenHelper;
        $this->permissionsModel = $permissionsModel;
        $this->permissionsCache = $permissionsCache;
        $this->permissionsValidator = $permissionsValidator;
    }

    public function actionCreate()
    {
        if ($this->request->getPost()->isEmpty() === false) {
            $this->_createPost($this->request->getPost()->getAll());
        }

        $this->view->assign('form', array_merge(['name' => ''], $this->request->getPost()->getAll()));

        $roles = $this->acl->getAllRoles();
        $c_roles = count($roles);
        for ($i = 0; $i < $c_roles; ++$i) {
            $roles[$i]['selected'] = $this->get('core.helpers.forms')->selectEntry('roles', $roles[$i]['id'], !empty($parent[0]['id']) ? $parent[0]['id'] : 0);
            $roles[$i]['name'] = str_repeat('&nbsp;&nbsp;', $roles[$i]['level']) . $roles[$i]['name'];
        }
        $this->view->assign('parent', $roles);

        $modules = $this->modules->getActiveModules();
        $privileges = $this->acl->getAllPrivileges();
        $c_privileges = count($privileges);
        $this->view->assign('privileges', $privileges);

        foreach ($modules as $module => $moduleInfo) {
            for ($j = 0; $j < $c_privileges; ++$j) {
                $privileges[$j]['select'] = $this->generatePrivilegeCheckboxes(
                    0,
                    $moduleInfo['id'],
                    $privileges[$j]['id'],
                    2
                );
            }
            $modules[$module]['privileges'] = $privileges;
        }

        $this->view->assign('modules', $modules);

        $this->formTokenHelper->generateFormToken($this->request->getQuery());
    }

    /**
     * @param string $action
     *
     * @throws \ACP3\Core\Exceptions\ResultNotExists
     */
    public function actionDelete($action = '')
    {
        $this->handleCustomDeleteAction(
            $action,
            function($items) {
                $bool = $bool2 = $bool3 = false;
                $levelNotDeletable = false;

                foreach ($items as $item) {
                    if (in_array($item, [1, 2, 4]) === true) {
                        $levelNotDeletable = true;
                    } else {
                        $bool = $this->nestedSet->deleteNode($item, Permissions\Model::TABLE_NAME);
                        $bool2 = $this->permissionsModel->delete($item, 'role_id', Permissions\Model::TABLE_NAME_RULES);
                        $bool3 = $this->permissionsModel->delete($item, 'role_id', Permissions\Model::TABLE_NAME_USER_ROLES);
                    }
                }

                $this->permissionsCache->getCacheDriver()->deleteAll();

                if ($levelNotDeletable === true) {
                    $result = !$levelNotDeletable;
                    $text = $this->lang->t('permissions', 'role_not_deletable');
                } else {
                    $result = $bool !== false && $bool2 !== false && $bool3 !== false;
                    $text = $this->lang->t('system', $result ? 'delete_success' : 'delete_error');
                }

                $this->redirectMessages()->setMessage($result, $text);
            }
        );
    }

    /**
     * @param int $id
     *
     * @throws \ACP3\Core\Exceptions\ResultNotExists
     */
    public function actionEdit($id)
    {
        $role = $this->permissionsModel->getRoleById($id);

        if (!empty($role)) {
            $this->breadcrumb->setTitlePostfix($role['name']);

            if ($this->request->getPost()->isEmpty() === false) {
                $this->_editPost($this->request->getPost()->getAll(), $id);
            }

            if ($this->request->getParameters()->get('id', 0) != 1) {
                $roles = $this->acl->getAllRoles();
                $c_roles = count($roles);
                for ($i = 0; $i < $c_roles; ++$i) {
                    if ($roles[$i]['left_id'] >= $role['left_id'] && $roles[$i]['right_id'] <= $role['right_id']) {
                        unset($roles[$i]);
                    } else {
                        $roles[$i]['selected'] = $this->get('core.helpers.forms')->selectEntry('roles', $roles[$i]['id'], $role['parent_id']);
                        $roles[$i]['name'] = str_repeat('&nbsp;&nbsp;', $roles[$i]['level']) . $roles[$i]['name'];
                    }
                }
                $this->view->assign('parent', $roles);
            }

            $rules = $this->acl->getRules([$id]);
            $modules = $this->modules->getActiveModules();
            $privileges = $this->acl->getAllPrivileges();
            $c_privileges = count($privileges);
            $this->view->assign('privileges', $privileges);

            foreach ($modules as $name => $moduleInfo) {
                $moduleDir = strtolower($moduleInfo['dir']);
                for ($j = 0; $j < $c_privileges; ++$j) {
                    $privilegeValue = isset($rules[$moduleDir][$privileges[$j]['key']]['permission']) ? $rules[$moduleDir][$privileges[$j]['key']]['permission'] : 0;
                    $privileges[$j]['select'] = $this->generatePrivilegeCheckboxes(
                        $id,
                        $moduleInfo['id'],
                        $privileges[$j]['id'],
                        (int) $privilegeValue
                    );
                    //$privileges[$j]['calculated'] = sprintf($this->lang->t('permissions', 'calculated_permission'), $rules[$privileges[$j]['key']]['access'] === true ? $this->lang->t('permissions', 'allow_access') :  $this->lang->t('permissions', 'deny_access'));
                }
                $modules[$name]['privileges'] = $privileges;
            }

            $this->view->assign('modules', $modules);

            $this->view->assign('form', array_merge($role, $this->request->getPost()->getAll()));

            $this->formTokenHelper->generateFormToken($this->request->getQuery());
        } else {
            throw new Core\Exceptions\ResultNotExists();
        }
    }

    public function actionIndex()
    {
        $roles = $this->acl->getAllRoles();
        $c_roles = count($roles);

        if ($c_roles > 0) {
            for ($i = 0; $i < $c_roles; ++$i) {
                $roles[$i]['spaces'] = str_repeat('&nbsp;&nbsp;', $roles[$i]['level']);
            }
            $this->view->assign('roles', $roles);
            $this->view->assign('can_delete', $this->acl->hasPermission('admin/permissions/index/delete'));
            $this->view->assign('can_order', $this->acl->hasPermission('admin/permissions/index/order'));
        }
    }

    /**
     * @param int    $id
     * @param string $action
     *
     * @throws \ACP3\Core\Exceptions\ResultNotExists
     */
    public function actionOrder($id, $action)
    {
        if ($this->permissionsModel->roleExists($id) === true) {
            $this->nestedSet->sort(
                $id,
                $action,
                Permissions\Model::TABLE_NAME
            );

            $this->permissionsCache->getCacheDriver()->deleteAll();

            $this->redirect()->temporary('acp/permissions');
        } else {
            throw new Core\Exceptions\ResultNotExists();
        }
    }

    /**
     * @param array $formData
     *
     * @throws \Doctrine\DBAL\ConnectionException
     */
    protected function _createPost(array $formData)
    {
        $this->handleCreatePostAction(function() use ($formData) {
            $this->permissionsValidator->validate($formData);

            $insertValues = [
                'id' => '',
                'name' => Core\Functions::strEncode($formData['name']),
                'parent_id' => $formData['parent_id'],
            ];

            $roleId = $this->nestedSet->insertNode(
                (int)$formData['parent_id'],
                $insertValues,
                Permissions\Model::TABLE_NAME,
                true
            );

            foreach ($formData['privileges'] as $moduleId => $privileges) {
                foreach ($privileges as $id => $permission) {
                    $ruleInsertValues = [
                        'id' => '',
                        'role_id' => $roleId,
                        'module_id' => $moduleId,
                        'privilege_id' => $id,
                        'permission' => $permission
                    ];
                    $this->permissionsModel->insert($ruleInsertValues, Permissions\Model::TABLE_NAME_RULES);
                }
            }

            $this->permissionsCache->saveRolesCache();

            $this->formTokenHelper->unsetFormToken($this->request->getQuery());

            return $roleId;
        });
    }

    /**
     * @param array $formData
     * @param int   $id
     *
     * @throws \Doctrine\DBAL\ConnectionException
     */
    protected function _editPost(array $formData, $id)
    {
        $this->handleEditPostAction(function() use ($formData, $id) {
            $this->permissionsValidator->validate($formData, $id);

            $updateValues = [
                'name' => Core\Functions::strEncode($formData['name']),
                'parent_id' => $id === 1 ? 0 : $formData['parent_id'],
            ];

            $bool = $this->nestedSet->editNode(
                $id,
                $id === 1 ? '' : (int)$formData['parent_id'],
                0,
                $updateValues,
                Permissions\Model::TABLE_NAME
            );

            // Bestehende Berechtigungen löschen, da in der Zwischenzeit neue hinzugekommen sein könnten
            $this->permissionsModel->delete($id, 'role_id', Permissions\Model::TABLE_NAME_RULES);
            foreach ($formData['privileges'] as $moduleId => $privileges) {
                foreach ($privileges as $privilegeId => $permission) {
                    $ruleInsertValues = [
                        'id' => '',
                        'role_id' => $id,
                        'module_id' => $moduleId,
                        'privilege_id' => $privilegeId,
                        'permission' => $permission
                    ];
                    $this->permissionsModel->insert($ruleInsertValues, Permissions\Model::TABLE_NAME_RULES);
                }
            }

            $this->permissionsCache->getCacheDriver()->deleteAll();

            $this->formTokenHelper->unsetFormToken($this->request->getQuery());

            return $bool;
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
            0 => $this->lang->t('permissions', 'deny_access'),
            1 => $this->lang->t('permissions', 'allow_access'),
            2 => $this->lang->t('permissions', 'inherit_access')
        ];

        $select = [];
        foreach ($permissions as $value => $lang) {
            if ($roleId === 1 && $value === 2) {
                continue;
            }

            $select[$value]['value'] = $value;
            $select[$value]['selected'] = $this->privilegeIsChecked($moduleId, $privilegeId, $value, $defaultValue);
            $select[$value]['lang'] = $lang;
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
        if ($this->request->getPost()->isEmpty() && $defaultValue === $value ||
            !$this->request->getPost()->isEmpty() && (int) $this->request->getPost()->get('privileges')[$moduleId][$privilegeId] === $value
        ) {
            return ' checked="checked"';
        }

        return '';
    }

}
