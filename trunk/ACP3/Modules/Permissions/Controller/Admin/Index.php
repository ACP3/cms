<?php

namespace ACP3\Modules\Permissions\Controller\Admin;

use ACP3\Core;
use ACP3\Modules\Permissions;

/**
 * Description of PermissionsAdmin
 *
 * @author Tino Goratsch
 */
class Index extends Core\Modules\Controller\Admin
{
    /**
     * @var Permissions\Model
     */
    protected $model;

    public function preDispatch()
    {
        parent::preDispatch();

        $this->model = new Permissions\Model($this->db);
    }

    public function actionCreate()
    {
        if (empty($_POST) === false) {
            try {
                $validator = new Permissions\Validator($this->lang, $this->uri, $this->model);
                $validator->validateCreate($_POST);

                $this->db->beginTransaction();

                $insertValues = array(
                    'id' => '',
                    'name' => Core\Functions::strEncode($_POST['name']),
                    'parent_id' => $_POST['parent'],
                );

                $nestedSet = new Core\NestedSet($this->db, 'acl_roles');
                $bool = $nestedSet->insertNode((int)$_POST['parent'], $insertValues);
                $roleId = $this->db->lastInsertId();

                foreach ($_POST['privileges'] as $moduleId => $privileges) {
                    foreach ($privileges as $id => $permission) {
                        $ruleInsertValues = array(
                            'id' => '',
                            'role_id' => $roleId,
                            'module_id' => $moduleId,
                            'privilege_id' => $id,
                            'permission' => $permission
                        );
                        $this->model->insert($ruleInsertValues, Permissions\Model::TABLE_NAME_RULES);
                    }
                }

                $this->db->commit();

                $this->get('ACL')->setRolesCache();

                $this->session->unsetFormToken();

                Core\Functions::setRedirectMessage($bool, $this->lang->t('system', $bool !== false ? 'create_success' : 'create_error'), 'acp/permissions');
            } catch (Core\Exceptions\InvalidFormToken $e) {
                Core\Functions::setRedirectMessage(false, $e->getMessage(), 'acp/permissions');
            } catch (Core\Exceptions\ValidationFailed $e) {
                $alerts = new Core\Helpers\Alerts($this->uri, $this->view);
                $this->view->assign('error_msg', $alerts->errorBox($e->getMessage()));
            }
        }

        $this->view->assign('form', array_merge(array('name' => ''), $_POST));

        $roles = $this->get('ACL')->getAllRoles();
        $c_roles = count($roles);
        for ($i = 0; $i < $c_roles; ++$i) {
            $roles[$i]['selected'] = Core\Functions::selectEntry('roles', $roles[$i]['id'], !empty($parent[0]['id']) ? $parent[0]['id'] : 0);
            $roles[$i]['name'] = str_repeat('&nbsp;&nbsp;', $roles[$i]['level']) . $roles[$i]['name'];
        }
        $this->view->assign('parent', $roles);

        $modules = Core\Modules::getActiveModules();
        $privileges = $this->get('ACL')->getAllPrivileges();
        $c_privileges = count($privileges);
        $this->view->assign('privileges', $privileges);

        foreach ($modules as $module => $params) {
            for ($j = 0; $j < $c_privileges; ++$j) {
                // Für jede Privilegie ein Input-Feld zuweisen
                $select = array();
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

        $this->session->generateFormToken();
    }

    public function actionDelete()
    {
        $items = $this->_deleteItem('acp/permissions/index/delete', 'acp/permissions');

        if ($this->uri->action === 'confirmed') {
            $bool = $bool2 = $bool3 = false;
            $levelUndeletable = false;

            $nestedSet = new Core\NestedSet($this->db, 'acl_roles');
            foreach ($items as $item) {
                if (in_array($item, array(1, 2, 4)) === true) {
                    $levelUndeletable = true;
                } else {
                    $bool = $nestedSet->deleteNode($item);
                    $bool2 = $this->model->delete($item, 'role_id', Permissions\Model::TABLE_NAME_RULES);
                    $bool3 = $this->model->delete($item, 'role_id', Permissions\Model::TABLE_NAME_USER_ROLES);
                }
            }

            $cache = new Core\Cache2('acl');
            $cache->getDriver()->deleteAll();

            if ($levelUndeletable === true) {
                $text = $this->lang->t('permissions', 'role_undeletable');
            } else {
                $text = $this->lang->t('system', $bool !== false && $bool2 !== false && $bool3 !== false ? 'delete_success' : 'delete_error');
            }
            Core\Functions::setRedirectMessage($bool && $bool2 && $bool3, $text, 'acp/permissions');
        } elseif (is_string($items)) {
            throw new Core\Exceptions\ResultNotExists();
        }
    }

    public function actionEdit()
    {
        $role = $this->model->getRoleById((int) $this->uri->id);

        if (!empty($role)) {
            if (empty($_POST) === false) {
                try {
                    $validator = new Permissions\Validator($this->lang, $this->uri, $this->model);
                    $validator->validateEdit($_POST);

                    $updateValues = array(
                        'name' => Core\Functions::strEncode($_POST['name']),
                        'parent_id' => $this->uri->id == 1 ? 0 : $_POST['parent'],
                    );
                    $nestedSet = new Core\NestedSet($this->db, 'acl_roles');
                    $bool = $nestedSet->EditNode($this->uri->id, $this->uri->id == 1 ? '' : (int)$_POST['parent'], 0, $updateValues);

                    $this->db->beginTransaction();
                    // Bestehende Berechtigungen löschen, da in der Zwischenzeit neue hinzugekommen sein könnten
                    $this->model->delete($this->uri->id, 'role_id', Permissions\Model::TABLE_NAME_RULES);
                    foreach ($_POST['privileges'] as $moduleId => $privileges) {
                        foreach ($privileges as $id => $permission) {
                            $ruleInsertValues = array('id' => '', 'role_id' => $this->uri->id, 'module_id' => $moduleId, 'privilege_id' => $id, 'permission' => $permission);
                            $this->model->insert($ruleInsertValues, Permissions\Model::TABLE_NAME_RULES);
                        }
                    }
                    $this->db->commit();

                    $cache = new Core\Cache2('acl');
                    $cache->getDriver()->deleteAll();

                    $this->session->unsetFormToken();

                    Core\Functions::setRedirectMessage($bool, $this->lang->t('system', $bool !== false ? 'edit_success' : 'edit_error'), 'acp/permissions');
                } catch (Core\Exceptions\InvalidFormToken $e) {
                    Core\Functions::setRedirectMessage(false, $e->getMessage(), 'acp/permissions');
                } catch (Core\Exceptions\ValidationFailed $e) {
                    $alerts = new Core\Helpers\Alerts($this->uri, $this->view);
                    $this->view->assign('error_msg', $alerts->errorBox($e->getMessage()));
                }
            }

            if ($this->uri->id != 1) {
                $roles = $this->get('ACL')->getAllRoles();
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

            $rules = $this->get('ACL')->getRules(array($this->uri->id));
            $modules = Core\Modules::getActiveModules();
            $privileges = $this->get('ACL')->getAllPrivileges();
            $c_privileges = count($privileges);
            $this->view->assign('privileges', $privileges);

            foreach ($modules as $name => $params) {
                $moduleDir = strtolower($params['dir']);
                for ($j = 0; $j < $c_privileges; ++$j) {
                    $privilegeValue = isset($rules[$moduleDir][$privileges[$j]['key']]['permission']) ? $rules[$moduleDir][$privileges[$j]['key']]['permission'] : 0;
                    $select = array();
                    $select[0]['value'] = 0;
                    $select[0]['selected'] = !empty($_POST) === false && $privilegeValue == 0 || empty($_POST) === false && $_POST['privileges'][$params['id']][$privileges[$j]['id']] == 0 ? ' checked="checked"' : '';
                    $select[0]['lang'] = $this->lang->t('permissions', 'deny_access');
                    $select[1]['value'] = 1;
                    $select[1]['selected'] = !empty($_POST) === false && $privilegeValue == 1 || empty($_POST) === false && $_POST['privileges'][$params['id']][$privileges[$j]['id']] == 1 ? ' checked="checked"' : '';
                    $select[1]['lang'] = $this->lang->t('permissions', 'allow_access');
                    if ($this->uri->id != 1) {
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

            $this->session->generateFormToken();
        } else {
            throw new Core\Exceptions\ResultNotExists();
        }
    }

    public function actionIndex()
    {
        Core\Functions::getRedirectMessage();

        $roles = $this->get('ACL')->getAllRoles();
        $c_roles = count($roles);

        if ($c_roles > 0) {
            for ($i = 0; $i < $c_roles; ++$i) {
                $roles[$i]['spaces'] = str_repeat('&nbsp;&nbsp;', $roles[$i]['level']);
            }
            $this->view->assign('roles', $roles);
            $this->view->assign('can_delete', Core\Modules::hasPermission('admin/permissions/index/delete'));
            $this->view->assign('can_order', Core\Modules::hasPermission('admin/permissions/index/order'));
        }
    }

    public function actionOrder()
    {
        if (Core\Validate::isNumber($this->uri->id) === true && $this->model->roleExists($this->uri->id) === true) {
            $nestedSet = new Core\NestedSet($this->db, 'acl_roles');
            $nestedSet->order($this->uri->id, $this->uri->action);

            $cache = new Core\Cache2('acl');
            $cache->getDriver()->deleteAll();

            $this->uri->redirect('acp/permissions');
        } else {
            throw new Core\Exceptions\ResultNotExists();
        }
    }
}