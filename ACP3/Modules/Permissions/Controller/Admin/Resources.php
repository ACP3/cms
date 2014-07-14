<?php

namespace ACP3\Modules\Permissions\Controller\Admin;

use ACP3\Core;
use ACP3\Modules\Permissions;

/**
 * Class Resources
 * @package ACP3\Modules\Permissions\Controller\Admin
 */
class Resources extends Core\Modules\Controller\Admin
{
    /**
     * @var Core\ACL
     */
    protected $acl;
    /**
     * @var Permissions\Model
     */
    protected $permissionsModel;

    public function __construct(
        Core\Context $context,
        Core\Breadcrumb $breadcrumb,
        Core\SEO $seo,
        Core\Validate $validate,
        Core\Session $session,
        Core\ACL $acl,
        Permissions\Model $permissionsModel)
    {
        parent::__construct($context, $breadcrumb, $seo, $validate, $session);

        $this->acl = $acl;
        $this->permissionsModel = $permissionsModel;
    }

    public function actionCreate()
    {
        if (empty($_POST) === false) {
            try {
                $validator = $this->get('permissions.validator');
                $validator->validateCreateResource($_POST);

                $moduleInfo = $this->modules->getModuleInfo($_POST['modules']);
                $insertValues = array(
                    'id' => '',
                    'module_id' => $moduleInfo['id'],
                    'area' => $_POST['area'],
                    'controller' => $_POST['controller'],
                    'page' => $_POST['resource'],
                    'params' => '',
                    'privilege_id' => $_POST['privileges'],
                );
                $bool = $this->permissionsModel->insert($insertValues, Permissions\Model::TABLE_NAME_RESOURCES);

                $cache = new Permissions\Cache($this->permissionsModel);
                $cache->setResourcesCache();

                $this->session->unsetFormToken();

                $this->redirectMessages()->setMessage($bool, $this->lang->t('system', $bool !== false ? 'create_success' : 'create_error'), 'acp/permissions/resources');
            } catch (Core\Exceptions\InvalidFormToken $e) {
                $this->redirectMessages()->setMessage(false, $e->getMessage(), 'acp/permissions/resources');
            } catch (Core\Exceptions\ValidationFailed $e) {
                $alerts = new Core\Helpers\Alerts($this->uri, $this->view);
                $this->view->assign('error_msg', $alerts->errorBox($e->getMessage()));
            }
        }

        $modules = $this->modules->getActiveModules();
        foreach ($modules as $row) {
            $modules[$row['name']]['selected'] = Core\Functions::selectEntry('modules', $row['name']);
        }
        $this->view->assign('modules', $modules);

        $privileges = $this->acl->getAllPrivileges();
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
                $bool = $this->permissionsModel->delete($item, Permissions\Model::TABLE_NAME_RESOURCES);
            }

            $cache = new Permissions\Cache($this->permissionsModel);
            $cache->setResourcesCache();

            $this->redirectMessages()->setMessage($bool, $this->lang->t('system', $bool !== false ? 'delete_success' : 'delete_error'), 'acp/permissions/resources');
        } elseif (is_string($items)) {
            throw new Core\Exceptions\ResultNotExists();
        }
    }

    public function actionEdit()
    {
        $resource = $this->permissionsModel->getResourceById((int) $this->uri->id);
        if (!empty($resource)) {
            if (empty($_POST) === false) {
                try {
                    $validator = $this->get('permissions.validator');
                    $validator->validateEditResource($_POST);

                    $updateValues = array(
                        'controller' => $_POST['controller'],
                        'area' => $_POST['area'],
                        'page' => $_POST['resource'],
                        'privilege_id' => $_POST['privileges'],
                    );
                    $bool = $this->permissionsModel->update($updateValues, $this->uri->id, Permissions\Model::TABLE_NAME_RESOURCES);

                    $cache = new Permissions\Cache($this->permissionsModel);
                    $cache->setResourcesCache();

                    $this->session->unsetFormToken();

                    $this->redirectMessages()->setMessage($bool, $this->lang->t('system', $bool !== false ? 'edit_success' : 'edit_error'), 'acp/permissions/resources');
                } catch (Core\Exceptions\InvalidFormToken $e) {
                    $this->redirectMessages()->setMessage(false, $e->getMessage(), 'acp/permissions/resources');
                } catch (Core\Exceptions\ValidationFailed $e) {
                    $alerts = new Core\Helpers\Alerts($this->uri, $this->view);
                    $this->view->assign('error_msg', $alerts->errorBox($e->getMessage()));
                }
            }

            $privileges = $this->acl->getAllPrivileges();
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
        $redirect = new Core\Helpers\RedirectMessages($this->uri, $this->view);
        $redirect->getMessage();

        $resources = $this->permissionsModel->getAllResources();
        $c_resources = count($resources);
        $output = array();
        for ($i = 0; $i < $c_resources; ++$i) {
            if ($this->modules->isActive($resources[$i]['module_name']) === true) {
                $module = $this->lang->t($resources[$i]['module_name'], $resources[$i]['module_name']);
                $output[$module][] = $resources[$i];
            }
        }
        ksort($output);
        $this->view->assign('resources', $output);
        $this->view->assign('can_delete_resource', $this->modules->hasPermission('admin/permissions/resources/delete'));
    }
}