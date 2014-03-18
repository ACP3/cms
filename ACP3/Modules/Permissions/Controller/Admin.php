<?php

namespace ACP3\Modules\Permissions\Controller;

use ACP3\Core;
use ACP3\Modules\Permissions;

/**
 * Description of PermissionsAdmin
 *
 * @author Tino Goratsch
 */
class Admin extends Core\Modules\Controller\Admin
{
    /**
     * @var Permissions\Model
     */
    protected $model;

    protected function _init()
    {
        $this->model = new Permissions\Model($this->db, $this->lang, $this->uri);
    }

    public function actionCreate()
    {
        if (isset($_POST['submit']) === true) {
            try {
                $this->model->validateCreate($_POST);

                $this->db->beginTransaction();

                $insertValues = array(
                    'id' => '',
                    'name' => Core\Functions::strEncode($_POST['name']),
                    'parent_id' => $_POST['parent'],
                );

                $nestedSet = new Core\NestedSet($this->db, 'acl_roles');
                $bool = $nestedSet->insertNode((int)$_POST['parent'], $insertValues);
                $roleId = $this->db->lastInsertId();

                foreach ($_POST['privileges'] as $module_id => $privileges) {
                    foreach ($privileges as $id => $permission) {
                        $ruleInsertValues = array('id' => '', 'role_id' => $roleId, 'module_id' => $module_id, 'privilege_id' => $id, 'permission' => $permission);
                        $this->model->insert($ruleInsertValues, Permissions\Model::TABLE_NAME_RULES);
                    }
                }

                $this->db->commit();

                Core\ACL::setRolesCache();

                $this->session->unsetFormToken();

                Core\Functions::setRedirectMessage($bool, $this->lang->t('system', $bool !== false ? 'create_success' : 'create_error'), 'acp/permissions');
            } catch (Core\Exceptions\InvalidFormToken $e) {
                Core\Functions::setRedirectMessage(false, $e->getMessage(), 'acp/permissions');
            } catch (Core\Exceptions\ValidationFailed $e) {
                $this->view->assign('error_msg', $e->getMessage());
            }
        }

        $this->view->assign('form', isset($_POST['submit']) ? $_POST : array('name' => ''));

        $roles = Core\ACL::getAllRoles();
        $c_roles = count($roles);
        for ($i = 0; $i < $c_roles; ++$i) {
            $roles[$i]['selected'] = Core\Functions::selectEntry('roles', $roles[$i]['id'], !empty($parent[0]['id']) ? $parent[0]['id'] : 0);
            $roles[$i]['name'] = str_repeat('&nbsp;&nbsp;', $roles[$i]['level']) . $roles[$i]['name'];
        }
        $this->view->assign('parent', $roles);

        $modules = Core\Modules::getActiveModules();
        $privileges = Core\ACL::getAllPrivileges();
        $c_privileges = count($privileges);
        $this->view->assign('privileges', $privileges);

        foreach ($modules as $module => $params) {
            for ($j = 0; $j < $c_privileges; ++$j) {
                // Für jede Privilegie ein Input-Feld zuweisen
                $select = array();
                $select[0]['value'] = 0;
                $select[0]['selected'] = isset($_POST['submit']) && $_POST['privileges'][$params['id']][$privileges[$j]['id']] == 0 ? ' checked="checked"' : '';
                $select[0]['lang'] = $this->lang->t('permissions', 'deny_access');
                $select[1]['value'] = 1;
                $select[1]['selected'] = isset($_POST['submit']) && $_POST['privileges'][$params['id']][$privileges[$j]['id']] == 1 ? ' checked="checked"' : '';
                $select[1]['lang'] = $this->lang->t('permissions', 'allow_access');
                $select[2]['value'] = 2;
                $select[2]['selected'] = !isset($_POST['submit']) || isset($_POST['submit']) && $_POST['privileges'][$params['id']][$privileges[$j]['id']] == 2 ? ' checked="checked"' : '';
                $select[2]['lang'] = $this->lang->t('permissions', 'inherit_access');
                $privileges[$j]['select'] = $select;
            }
            $modules[$module]['privileges'] = $privileges;
        }

        $this->view->assign('modules', $modules);

        $this->session->generateFormToken();
    }

    public function actionCreateResource()
    {
        $this->breadcrumb
            ->append($this->lang->t('permissions', 'acp_list_resources'), $this->uri->route('acp/permissions/list_resources'))
            ->append($this->lang->t('permissions', 'acp_create_resource'));

        if (isset($_POST['submit']) === true) {
            try {
                $this->model->validateCreateResource($_POST);

                $moduleInfo = Core\Modules::getModuleInfo($_POST['modules']);
                $insertValues = array(
                    'id' => '',
                    'module_id' => $moduleInfo['id'],
                    'page' => $_POST['resource'],
                    'params' => '',
                    'privilege_id' => $_POST['privileges'],
                );
                $bool = $this->model->insert($insertValues, Permissions\Model::TABLE_NAME_RESOURCES);

                Core\ACL::setResourcesCache();

                $this->session->unsetFormToken();

                Core\Functions::setRedirectMessage($bool, $this->lang->t('system', $bool !== false ? 'create_success' : 'create_error'), 'acp/permissions/list_resources');
            } catch (Core\Exceptions\InvalidFormToken $e) {
                Core\Functions::setRedirectMessage(false, $e->getMessage(), 'acp/permissions/list_resources');
            } catch (Core\Exceptions\ValidationFailed $e) {
                $this->view->assign('error_msg', $e->getMessage());
            }
        }

        $modules = Core\Modules::getActiveModules();
        foreach ($modules as $row) {
            $modules[$row['name']]['selected'] = Core\Functions::selectEntry('modules', $row['name']);
        }
        $this->view->assign('modules', $modules);

        $privileges = Core\ACL::getAllPrivileges();
        $c_privileges = count($privileges);
        for ($i = 0; $i < $c_privileges; ++$i) {
            $privileges[$i]['selected'] = Core\Functions::selectEntry('privileges', $privileges[$i]['id']);
        }
        $this->view->assign('privileges', $privileges);

        $this->view->assign('form', isset($_POST['submit']) ? $_POST : array('resource' => ''));

        $this->session->generateFormToken();
    }

    public function actionDelete()
    {
        $items = $this->_deleteItem('acp/permissions/delete', 'acp/permissions');

        if ($this->uri->action === 'confirmed') {
            $items = explode('|', $items);
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

            Core\Cache::purge(0, 'acl');

            if ($levelUndeletable === true) {
                $text = $this->lang->t('permissions', 'role_undeletable');
            } else {
                $text = $this->lang->t('system', $bool !== false && $bool2 !== false && $bool3 !== false ? 'delete_success' : 'delete_error');
            }
            Core\Functions::setRedirectMessage($bool && $bool2 && $bool3, $text, 'acp/permissions');
        } elseif (is_string($items)) {
            $this->uri->redirect('errors/404');
        }
    }

    public function actionDeleteResources()
    {
        $this->breadcrumb
            ->append($this->lang->t('permissions', 'acp_list_resources'), $this->uri->route('acp/permissions/acp_list_resources'))
            ->append($this->lang->t('permissions', 'delete_resources'));

        $items = $this->_deleteItem('acp/permissions/delete_resources', 'acp/permissions/list_resources');

        if ($this->uri->action === 'confirmed') {
            $items = explode('|', $items);
            $bool = false;

            foreach ($items as $item) {
                $bool = $this->db->delete(DB_PRE . 'acl_resources', array('id' => $item));
            }

            Core\ACL::setResourcesCache();

            Core\Functions::setRedirectMessage($bool, $this->lang->t('system', $bool !== false ? 'delete_success' : 'delete_error'), 'acp/permissions/list_resources');
        } elseif (is_string($items)) {
            $this->uri->redirect('errors/404');
        }
    }

    public function actionEdit()
    {
        if (Core\Validate::isNumber($this->uri->id) === true && $this->model->roleExists($this->uri->id) === true) {
            if (isset($_POST['submit']) === true) {
                try {
                    $this->model->validateEdit($_POST);

                    $updateValues = array(
                        'name' => Core\Functions::strEncode($_POST['name']),
                        'parent_id' => $this->uri->id == 1 ? 0 : $_POST['parent'],
                    );
                    $nestedSet = new Core\NestedSet($this->db, 'acl_roles');
                    $bool = $nestedSet->EditNode($this->uri->id, $this->uri->id == 1 ? '' : (int)$_POST['parent'], 0, $updateValues);

                    $this->db->beginTransaction();
                    // Bestehende Berechtigungen löschen, da in der Zwischenzeit neue hinzugekommen sein könnten
                    $this->model->delete($this->uri->id, 'role_id', Permissions\Model::TABLE_NAME_RULES);
                    foreach ($_POST['privileges'] as $module_id => $privileges) {
                        foreach ($privileges as $id => $permission) {
                            $ruleInsertValues = array('id' => '', 'role_id' => $this->uri->id, 'module_id' => $module_id, 'privilege_id' => $id, 'permission' => $permission);
                            $this->model->insert($ruleInsertValues, Permissions\Model::TABLE_NAME_RULES);
                        }
                    }
                    $this->db->commit();

                    // Cache der ACL zurücksetzen
                    Core\Cache::purge(0, 'acl');

                    $this->session->unsetFormToken();

                    Core\Functions::setRedirectMessage($bool, $this->lang->t('system', $bool !== false ? 'edit_success' : 'edit_error'), 'acp/permissions');
                } catch (Core\Exceptions\InvalidFormToken $e) {
                    Core\Functions::setRedirectMessage(false, $e->getMessage(), 'acp/permissions');
                } catch (Core\Exceptions\ValidationFailed $e) {
                    $this->view->assign('error_msg', $e->getMessage());
                }
            }

            $role = $this->model->getRoleById($this->uri->id);

            if ($this->uri->id != 1) {
                $roles = Core\ACL::getAllRoles();
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

            $rules = Core\ACL::getRules(array($this->uri->id));
            $modules = Core\Modules::getActiveModules();
            $privileges = Core\ACL::getAllPrivileges();
            $c_privileges = count($privileges);
            $this->view->assign('privileges', $privileges);

            foreach ($modules as $name => $params) {
                $moduleDir = strtolower($params['dir']);
                for ($j = 0; $j < $c_privileges; ++$j) {
                    $privilegeValue = isset($rules[$moduleDir][$privileges[$j]['key']]['permission']) ? $rules[$moduleDir][$privileges[$j]['key']]['permission'] : 0;
                    $select = array();
                    $select[0]['value'] = 0;
                    $select[0]['selected'] = !isset($_POST['submit']) && $privilegeValue == 0 || isset($_POST['submit']) && $_POST['privileges'][$params['id']][$privileges[$j]['id']] == 0 ? ' checked="checked"' : '';
                    $select[0]['lang'] = $this->lang->t('permissions', 'deny_access');
                    $select[1]['value'] = 1;
                    $select[1]['selected'] = !isset($_POST['submit']) && $privilegeValue == 1 || isset($_POST['submit']) && $_POST['privileges'][$params['id']][$privileges[$j]['id']] == 1 ? ' checked="checked"' : '';
                    $select[1]['lang'] = $this->lang->t('permissions', 'allow_access');
                    if ($this->uri->id != 1) {
                        $select[2]['value'] = 2;
                        $select[2]['selected'] = !isset($_POST['submit']) && $privilegeValue == 2 || isset($_POST['submit']) && $_POST['privileges'][$params['id']][$privileges[$j]['id']] == 2 ? ' checked="checked"' : '';
                        $select[2]['lang'] = $this->lang->t('permissions', 'inherit_access');
                        //$privileges[$j]['calculated'] = sprintf($this->lang->t('permissions', 'calculated_permission'), $rules[$privileges[$j]['key']]['access'] === true ? $this->lang->t('permissions', 'allow_access') :  $this->lang->t('permissions', 'deny_access'));
                    }
                    $privileges[$j]['select'] = $select;
                }
                $modules[$name]['privileges'] = $privileges;
            }

            $this->view->assign('modules', $modules);

            $this->view->assign('form', isset($_POST['submit']) ? $_POST : $role);

            $this->session->generateFormToken();
        } else {
            $this->uri->redirect('errors/404');
        }
    }

    public function actionEditResource()
    {
        $this->breadcrumb
            ->append($this->lang->t('permissions', 'acp_list_resources'), $this->uri->route('acp/permissions/list_resources'))
            ->append($this->lang->t('permissions', 'acp_edit_resource'));

        if (Core\Validate::isNumber($this->uri->id) === true && $this->model->resourceExists($this->uri->id) === true) {
            if (isset($_POST['submit']) === true) {
                try {
                    $this->model->validateEditResource($_POST);

                    $updateValues = array(
                        'page' => $_POST['resource'],
                        'privilege_id' => $_POST['privileges'],
                    );
                    $bool = $this->model->update($updateValues, $this->uri->id . Permissions\Model::TABLE_NAME_RESOURCES);

                    Core\ACL::setResourcesCache();

                    $this->session->unsetFormToken();

                    Core\Functions::setRedirectMessage($bool, $this->lang->t('system', $bool !== false ? 'edit_success' : 'edit_error'), 'acp/permissions/list_resources');
                } catch (Core\Exceptions\InvalidFormToken $e) {
                    Core\Functions::setRedirectMessage(false, $e->getMessage(), 'acp/permissions/list_resources');
                } catch (Core\Exceptions\ValidationFailed $e) {
                    $this->view->assign('error_msg', $e->getMessage());
                }
            }

            $resource = $this->model->getResourceById($this->uri->id);

            $privileges = Core\ACL::getAllPrivileges();
            $c_privileges = count($privileges);
            for ($i = 0; $i < $c_privileges; ++$i) {
                $privileges[$i]['selected'] = Core\Functions::selectEntry('privileges', $privileges[$i]['id'], $resource['privilege_id']);
            }
            $this->view->assign('privileges', $privileges);

            $this->view->assign('form', isset($_POST['submit']) ? $_POST : array('resource' => $resource['page'], 'modules' => $resource['module_name']));

            $this->session->generateFormToken();
        } else {
            $this->uri->redirect('errors/404');
        }
    }

    public function actionList()
    {
        Core\Functions::getRedirectMessage();

        $roles = Core\ACL::getAllRoles();
        $c_roles = count($roles);

        if ($c_roles > 0) {
            for ($i = 0; $i < $c_roles; ++$i) {
                $roles[$i]['spaces'] = str_repeat('&nbsp;&nbsp;', $roles[$i]['level']);
            }
            $this->view->assign('roles', $roles);
            $this->view->assign('can_delete', Core\Modules::hasPermission('permissions', 'acp_delete'));
            $this->view->assign('can_order', Core\Modules::hasPermission('permissions', 'acp_order'));
        }
    }

    public function actionListResources()
    {
        Core\Functions::getRedirectMessage();

        $resources = $this->model->getAllResources();
        $c_resources = count($resources);
        $output = array();
        for ($i = 0; $i < $c_resources; ++$i) {
            if (Core\Modules::isActive($resources[$i]['module_name']) === true) {
                $module = $this->lang->t($resources[$i]['module_name'], $resources[$i]['module_name']);
                $output[$module][] = $resources[$i];
            }
        }
        ksort($output);
        $this->view->assign('resources', $output);
        $this->view->assign('can_delete_resource', Core\Modules::hasPermission('permissions', 'acp_delete_resources'));
    }

    public function actionOrder()
    {
        if (Core\Validate::isNumber($this->uri->id) === true && $this->model->roleExists($this->uri->id) === true) {
            $nestedSet = new Core\NestedSet($this->db, 'acl_roles');
            $nestedSet->order($this->uri->id, $this->uri->action);

            Core\Cache::purge(0, 'acl');

            $this->uri->redirect('acp/permissions');
        } else {
            $this->uri->redirect('errors/404');
        }
    }
}