<?php

namespace ACP3\Modules\Permissions\Controller\Admin;

use ACP3\Core;
use ACP3\Modules\Permissions;

/**
 * Description of PermissionsAdmin
 *
 * @author Tino Goratsch
 */
class Resources extends Core\Modules\Controller\Admin
{
    /**
     * @var Permissions\Model
     */
    protected $model;

    public function preDispatch()
    {
        parent::preDispatch();

        $this->model = new Permissions\Model($this->db, $this->lang, $this->uri);
    }

    public function actionCreate()
    {
        if (empty($_POST) === false) {
            try {
                $this->model->validateCreateResource($_POST);

                $moduleInfo = Core\Modules::getModuleInfo($_POST['modules']);
                $insertValues = array(
                    'id' => '',
                    'module_id' => $moduleInfo['id'],
                    'area' => $_POST['area'],
                    'controller' => $_POST['controller'],
                    'page' => $_POST['resource'],
                    'params' => '',
                    'privilege_id' => $_POST['privileges'],
                );
                $bool = $this->model->insert($insertValues, Permissions\Model::TABLE_NAME_RESOURCES);

                Core\ACL::setResourcesCache();

                $this->session->unsetFormToken();

                Core\Functions::setRedirectMessage($bool, $this->lang->t('system', $bool !== false ? 'create_success' : 'create_error'), 'acp/permissions/resources');
            } catch (Core\Exceptions\InvalidFormToken $e) {
                Core\Functions::setRedirectMessage(false, $e->getMessage(), 'acp/permissions/resources');
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

        $this->view->assign('form', array_merge(array('resource' => '', 'area' => '', 'controller' => ''), $_POST));

        $this->session->generateFormToken();
    }

    public function actionDelete()
    {
        $items = $this->_deleteItem('acp/permissions/resources/delete', 'acp/permissions/resources');

        if ($this->uri->action === 'confirmed') {
            $bool = false;

            foreach ($items as $item) {
                $bool = $this->db->delete(DB_PRE . 'acl_resources', array('id' => $item));
            }

            Core\ACL::setResourcesCache();

            Core\Functions::setRedirectMessage($bool, $this->lang->t('system', $bool !== false ? 'delete_success' : 'delete_error'), 'acp/permissions/resources');
        } elseif (is_string($items)) {
            throw new Core\Exceptions\ResultNotExists();
        }
    }

    public function actionEdit()
    {
        if (Core\Validate::isNumber($this->uri->id) === true && $this->model->resourceExists($this->uri->id) === true) {
            if (empty($_POST) === false) {
                try {
                    $this->model->validateEditResource($_POST);

                    $updateValues = array(
                        'controller' => $_POST['controller'],
                        'area' => $_POST['area'],
                        'page' => $_POST['resource'],
                        'privilege_id' => $_POST['privileges'],
                    );
                    $bool = $this->model->update($updateValues, $this->uri->id, Permissions\Model::TABLE_NAME_RESOURCES);

                    Core\ACL::setResourcesCache();

                    $this->session->unsetFormToken();

                    Core\Functions::setRedirectMessage($bool, $this->lang->t('system', $bool !== false ? 'edit_success' : 'edit_error'), 'acp/permissions/resources');
                } catch (Core\Exceptions\InvalidFormToken $e) {
                    Core\Functions::setRedirectMessage(false, $e->getMessage(), 'acp/permissions/resources');
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

            $defaults = array(
                'resource' => $resource['page'],
                'area' => $resource['area'],
                'controller' => $resource['controller'],
                'modules' => $resource['module_name']
            );
            $this->view->assign('form', array_merge($defaults, $_POST));

            $this->session->generateFormToken();
        } else {
            throw new Core\Exceptions\ResultNotExists();
        }
    }

    public function actionIndex()
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
        $this->view->assign('can_delete_resource', Core\Modules::hasPermission('admin/permissions/resources/delete'));
    }
}