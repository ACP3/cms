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
     * @param Core\Helpers\Secure $secureHelper
     * @param Permissions\Model $permissionsModel
     * @param Permissions\Cache $permissionsCache
     */
    public function __construct(
        Core\Context\Admin $context,
        Core\Helpers\Secure $secureHelper,
        Permissions\Model $permissionsModel,
        Permissions\Cache $permissionsCache)
    {
        parent::__construct($context);

        $this->secureHelper = $secureHelper;
        $this->permissionsModel = $permissionsModel;
        $this->permissionsCache = $permissionsCache;
    }

    public function actionCreate()
    {
        if (empty($_POST) === false) {
            $this->_createPost($_POST);
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

        $this->secureHelper->generateFormToken($this->request->query);
    }

    public function actionDelete()
    {
        $items = $this->_deleteItem('acp/permissions/resources/delete', 'acp/permissions/resources');

        if ($this->request->action === 'confirmed') {
            $bool = false;

            foreach ($items as $item) {
                $bool = $this->permissionsModel->delete($item, Permissions\Model::TABLE_NAME_RESOURCES);
            }

            $this->permissionsCache->setResourcesCache();

            $this->redirectMessages()->setMessage($bool, $this->lang->t('system', $bool !== false ? 'delete_success' : 'delete_error'), 'acp/permissions/resources');
        } elseif (is_string($items)) {
            throw new Core\Exceptions\ResultNotExists();
        }
    }

    public function actionEdit()
    {
        $resource = $this->permissionsModel->getResourceById((int)$this->request->id);
        if (!empty($resource)) {
            if (empty($_POST) === false) {
                $this->_editPost($_POST);
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

            $this->secureHelper->generateFormToken($this->request->query);
        } else {
            throw new Core\Exceptions\ResultNotExists();
        }
    }

    public function actionIndex()
    {
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
        $this->view->assign('can_delete_resource', $this->acl->hasPermission('admin/permissions/resources/delete'));
    }

    /**
     * @param array $formData
     */
    private function _createPost(array $formData)
    {
        try {
            $validator = $this->get('permissions.validator');
            $validator->validateCreateResource($formData);

            $moduleInfo = $this->modules->getModuleInfo($formData['modules']);
            $insertValues = array(
                'id' => '',
                'module_id' => $moduleInfo['id'],
                'area' => $formData['area'],
                'controller' => $formData['controller'],
                'page' => $formData['resource'],
                'params' => '',
                'privilege_id' => $formData['privileges'],
            );
            $bool = $this->permissionsModel->insert($insertValues, Permissions\Model::TABLE_NAME_RESOURCES);

            $this->permissionsCache->setResourcesCache();

            $this->secureHelper->unsetFormToken($this->request->query);

            $this->redirectMessages()->setMessage($bool, $this->lang->t('system', $bool !== false ? 'create_success' : 'create_error'), 'acp/permissions/resources');
        } catch (Core\Exceptions\InvalidFormToken $e) {
            $this->redirectMessages()->setMessage(false, $e->getMessage(), 'acp/permissions/resources');
        } catch (Core\Exceptions\ValidationFailed $e) {
            $this->view->assign('error_msg', $this->get('core.helpers.alerts')->errorBox($e->getMessage()));
        }
    }

    /**
     * @param array $formData
     */
    private function _editPost(array $formData)
    {
        try {
            $validator = $this->get('permissions.validator');
            $validator->validateEditResource($formData);

            $updateValues = array(
                'controller' => $formData['controller'],
                'area' => $formData['area'],
                'page' => $formData['resource'],
                'privilege_id' => $formData['privileges'],
            );
            $bool = $this->permissionsModel->update($updateValues, $this->request->id, Permissions\Model::TABLE_NAME_RESOURCES);

            $this->permissionsCache->setResourcesCache();

            $this->secureHelper->unsetFormToken($this->request->query);

            $this->redirectMessages()->setMessage($bool, $this->lang->t('system', $bool !== false ? 'edit_success' : 'edit_error'), 'acp/permissions/resources');
        } catch (Core\Exceptions\InvalidFormToken $e) {
            $this->redirectMessages()->setMessage(false, $e->getMessage(), 'acp/permissions/resources');
        } catch (Core\Exceptions\ValidationFailed $e) {
            $this->view->assign('error_msg', $this->get('core.helpers.alerts')->errorBox($e->getMessage()));
        }
    }
}