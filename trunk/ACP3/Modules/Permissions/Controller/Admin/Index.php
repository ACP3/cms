<?php

namespace ACP3\Modules\Permissions\Controller\Admin;

use ACP3\Core;
use ACP3\Modules\Permissions;

/**
 * Class Index
 * @package ACP3\Modules\Permissions\Controller\Admin
 */
class Index extends Core\Modules\Controller\Admin
{
    /**
     * @var \Doctrine\DBAL\Connection
     */
    protected $db;
    /**
     * @var \ACP3\Core\Helpers\Secure
     */
    protected $secureHelper;
    /**
     * @var Permissions\Model
     */
    protected $permissionsModel;
    /**
     * @var Permissions\Cache
     */
    protected $permissionsCache;

    /**
     * @param Core\Context\Admin $context
     * @param \Doctrine\DBAL\Connection $db
     * @param Core\Helpers\Secure $secureHelper
     * @param Permissions\Model $permissionsModel
     * @param Permissions\Cache $permissionsCache
     */
    public function __construct(
        Core\Context\Admin $context,
        \Doctrine\DBAL\Connection $db,
        Core\Helpers\Secure $secureHelper,
        Permissions\Model $permissionsModel,
        Permissions\Cache $permissionsCache)
    {
        parent::__construct($context);

        $this->db = $db;
        $this->secureHelper = $secureHelper;
        $this->permissionsModel = $permissionsModel;
        $this->permissionsCache = $permissionsCache;
    }

    public function actionCreate()
    {
        if (empty($_POST) === false) {
            $this->_createPost($_POST);
        }

        $this->view->assign('form', array_merge(array('name' => ''), $_POST));

        $roles = $this->acl->getAllRoles();
        $c_roles = count($roles);
        for ($i = 0; $i < $c_roles; ++$i) {
            $roles[$i]['selected'] = Core\Functions::selectEntry('roles', $roles[$i]['id'], !empty($parent[0]['id']) ? $parent[0]['id'] : 0);
            $roles[$i]['name'] = str_repeat('&nbsp;&nbsp;', $roles[$i]['level']) . $roles[$i]['name'];
        }
        $this->view->assign('parent', $roles);

        $modules = $this->modules->getActiveModules();
        $privileges = $this->acl->getAllPrivileges();
        $c_privileges = count($privileges);
        $this->view->assign('privileges', $privileges);

        foreach ($modules as $module => $params) {
            for ($j = 0; $j < $c_privileges; ++$j) {
                // Für jede Privilegie ein Input-Feld zuweisen
                $select = [];
                $select[0]['value'] = 0;
                $select[0]['selected'] = empty($_POST) === false && $_POST['privileges'][$params['id']][$privileges[$j]['id']] == 0 ? ' checked="checked"' : '';
                $select[0]['lang'] = $this->lang->t('permissions', 'deny_access');
                $select[1]['value'] = 1;
                $select[1]['selected'] = empty($_POST) === false && $_POST['privileges'][$params['id']][$privileges[$j]['id']] == 1 ? ' checked="checked"' : '';
                $select[1]['lang'] = $this->lang->t('permissions', 'allow_access');
                $select[2]['value'] = 2;
                $select[2]['selected'] = !empty($_POST) === false || empty($_POST) === false && $_POST['privileges'][$params['id']][$privileges[$j]['id']] == 2 ? ' checked="checked"' : '';
                $select[2]['lang'] = $this->lang->t('permissions', 'inherit_access');
                $privileges[$j]['select'] = $select;
            }
            $modules[$module]['privileges'] = $privileges;
        }

        $this->view->assign('modules', $modules);

        $this->secureHelper->generateFormToken($this->request->query);
    }

    public function actionDelete()
    {
        $items = $this->_deleteItem('acp/permissions/index/delete', 'acp/permissions');

        if ($this->request->action === 'confirmed') {
            $bool = $bool2 = $bool3 = false;
            $levelUndeletable = false;

            $nestedSet = new Core\NestedSet($this->db, Permissions\Model::TABLE_NAME);
            foreach ($items as $item) {
                if (in_array($item, array(1, 2, 4)) === true) {
                    $levelUndeletable = true;
                } else {
                    $bool = $nestedSet->deleteNode($item);
                    $bool2 = $this->permissionsModel->delete($item, 'role_id', Permissions\Model::TABLE_NAME_RULES);
                    $bool3 = $this->permissionsModel->delete($item, 'role_id', Permissions\Model::TABLE_NAME_USER_ROLES);
                }
            }

            $cache = new Core\Cache('acl');
            $cache->getDriver()->deleteAll();

            if ($levelUndeletable === true) {
                $text = $this->lang->t('permissions', 'role_undeletable');
            } else {
                $text = $this->lang->t('system', $bool !== false && $bool2 !== false && $bool3 !== false ? 'delete_success' : 'delete_error');
            }

            $this->redirectMessages()->setMessage($bool && $bool2 && $bool3, $text, 'acp/permissions');
        } elseif (is_string($items)) {
            throw new Core\Exceptions\ResultNotExists();
        }
    }

    public function actionEdit()
    {
        $role = $this->permissionsModel->getRoleById((int)$this->request->id);

        if (!empty($role)) {
            if (empty($_POST) === false) {
                $this->_editPost($_POST);
            }

            if ($this->request->id != 1) {
                $roles = $this->acl->getAllRoles();
                $c_roles = count($roles);
                for ($i = 0; $i < $c_roles; ++$i) {
                    if ($roles[$i]['left_id'] >= $role['left_id'] && $roles[$i]['right_id'] <= $role['right_id']) {
                        unset($roles[$i]);
                    } else {
                        $roles[$i]['selected'] = Core\Functions::selectEntry('roles', $roles[$i]['id'], $role['parent_id']);
                        $roles[$i]['name'] = str_repeat('&nbsp;&nbsp;', $roles[$i]['level']) . $roles[$i]['name'];
                    }
                }
                $this->view->assign('parent', $roles);
            }

            $rules = $this->acl->getRules(array($this->request->id));
            $modules = $this->modules->getActiveModules();
            $privileges = $this->acl->getAllPrivileges();
            $c_privileges = count($privileges);
            $this->view->assign('privileges', $privileges);

            foreach ($modules as $name => $params) {
                $moduleDir = strtolower($params['dir']);
                for ($j = 0; $j < $c_privileges; ++$j) {
                    $privilegeValue = isset($rules[$moduleDir][$privileges[$j]['key']]['permission']) ? $rules[$moduleDir][$privileges[$j]['key']]['permission'] : 0;
                    $select = [];
                    $select[0]['value'] = 0;
                    $select[0]['selected'] = !empty($_POST) === false && $privilegeValue == 0 || empty($_POST) === false && $_POST['privileges'][$params['id']][$privileges[$j]['id']] == 0 ? ' checked="checked"' : '';
                    $select[0]['lang'] = $this->lang->t('permissions', 'deny_access');
                    $select[1]['value'] = 1;
                    $select[1]['selected'] = !empty($_POST) === false && $privilegeValue == 1 || empty($_POST) === false && $_POST['privileges'][$params['id']][$privileges[$j]['id']] == 1 ? ' checked="checked"' : '';
                    $select[1]['lang'] = $this->lang->t('permissions', 'allow_access');
                    if ($this->request->id != 1) {
                        $select[2]['value'] = 2;
                        $select[2]['selected'] = !empty($_POST) === false && $privilegeValue == 2 || empty($_POST) === false && $_POST['privileges'][$params['id']][$privileges[$j]['id']] == 2 ? ' checked="checked"' : '';
                        $select[2]['lang'] = $this->lang->t('permissions', 'inherit_access');
                        //$privileges[$j]['calculated'] = sprintf($this->lang->t('permissions', 'calculated_permission'), $rules[$privileges[$j]['key']]['access'] === true ? $this->lang->t('permissions', 'allow_access') :  $this->lang->t('permissions', 'deny_access'));
                    }
                    $privileges[$j]['select'] = $select;
                }
                $modules[$name]['privileges'] = $privileges;
            }

            $this->view->assign('modules', $modules);

            $this->view->assign('form', array_merge($role, $_POST));

            $this->secureHelper->generateFormToken($this->request->query);
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

    public function actionOrder()
    {
        if ($this->get('core.validator.rules.misc')->isNumber($this->request->id) === true && $this->permissionsModel->roleExists($this->request->id) === true) {
            $nestedSet = new Core\NestedSet($this->db, Permissions\Model::TABLE_NAME);
            $nestedSet->order($this->request->id, $this->request->action);

            $cache = new Core\Cache('acl');
            $cache->getDriver()->deleteAll();

            $this->redirect()->temporary('acp/permissions');
        } else {
            throw new Core\Exceptions\ResultNotExists();
        }
    }

    /**
     * @param array $formData
     * @throws \Doctrine\DBAL\ConnectionException
     */
    private function _createPost(array $formData)
    {
        try {
            $validator = $this->get('permissions.validator');
            $validator->validateCreate($formData);

            $this->db->beginTransaction();

            $insertValues = array(
                'id' => '',
                'name' => Core\Functions::strEncode($formData['name']),
                'parent_id' => $formData['parent'],
            );

            $nestedSet = new Core\NestedSet($this->db, Permissions\Model::TABLE_NAME);
            $bool = $nestedSet->insertNode((int)$formData['parent'], $insertValues);
            $roleId = $this->db->lastInsertId();

            foreach ($formData['privileges'] as $moduleId => $privileges) {
                foreach ($privileges as $id => $permission) {
                    $ruleInsertValues = array(
                        'id' => '',
                        'role_id' => $roleId,
                        'module_id' => $moduleId,
                        'privilege_id' => $id,
                        'permission' => $permission
                    );
                    $this->permissionsModel->insert($ruleInsertValues, Permissions\Model::TABLE_NAME_RULES);
                }
            }

            $this->db->commit();

            $this->permissionsCache->setRolesCache();

            $this->secureHelper->unsetFormToken($this->request->query);

            $this->redirectMessages()->setMessage($bool, $this->lang->t('system', $bool !== false ? 'create_success' : 'create_error'), 'acp/permissions');
        } catch (Core\Exceptions\InvalidFormToken $e) {
            $this->redirectMessages()->setMessage(false, $e->getMessage(), 'acp/permissions');
        } catch (Core\Exceptions\ValidationFailed $e) {
            $this->view->assign('error_msg', $this->get('core.helpers.alerts')->errorBox($e->getMessage()));
        }
    }

    /**
     * @param array $formData
     * @throws \Doctrine\DBAL\ConnectionException
     */
    private function _editPost(array $formData)
    {
        try {
            $validator = $this->get('permissions.validator');
            $validator->validateEdit($formData);

            $updateValues = array(
                'name' => Core\Functions::strEncode($formData['name']),
                'parent_id' => $this->request->id == 1 ? 0 : $formData['parent'],
            );
            $nestedSet = new Core\NestedSet($this->db, Permissions\Model::TABLE_NAME);
            $bool = $nestedSet->editNode($this->request->id, $this->request->id == 1 ? '' : (int)$formData['parent'], 0, $updateValues);

            $this->db->beginTransaction();
            // Bestehende Berechtigungen löschen, da in der Zwischenzeit neue hinzugekommen sein könnten
            $this->permissionsModel->delete($this->request->id, 'role_id', Permissions\Model::TABLE_NAME_RULES);
            foreach ($formData['privileges'] as $moduleId => $privileges) {
                foreach ($privileges as $id => $permission) {
                    $ruleInsertValues = array(
                        'id' => '',
                        'role_id' => $this->request->id,
                        'module_id' => $moduleId,
                        'privilege_id' => $id,
                        'permission' => $permission
                    );
                    $this->permissionsModel->insert($ruleInsertValues, Permissions\Model::TABLE_NAME_RULES);
                }
            }
            $this->db->commit();

            $cache = new Core\Cache('acl');
            $cache->getDriver()->deleteAll();

            $this->secureHelper->unsetFormToken($this->request->query);

            $this->redirectMessages()->setMessage($bool, $this->lang->t('system', $bool !== false ? 'edit_success' : 'edit_error'), 'acp/permissions');
        } catch (Core\Exceptions\InvalidFormToken $e) {
            $this->redirectMessages()->setMessage(false, $e->getMessage(), 'acp/permissions');
        } catch (Core\Exceptions\ValidationFailed $e) {
            $this->view->assign('error_msg', $this->get('core.helpers.alerts')->errorBox($e->getMessage()));
        }
    }
}