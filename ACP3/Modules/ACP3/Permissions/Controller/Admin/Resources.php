<?php

namespace ACP3\Modules\ACP3\Permissions\Controller\Admin;

use ACP3\Core;
use ACP3\Modules\ACP3\Permissions;

/**
 * Class Resources
 * @package ACP3\Modules\ACP3\Permissions\Controller\Admin
 */
class Resources extends Core\Modules\Controller\Admin
{
    /**
     * @var \ACP3\Core\Helpers\Secure
     */
    protected $secureHelper;
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
     * @param \ACP3\Core\Context\Admin            $context
     * @param \ACP3\Core\Helpers\Secure           $secureHelper
     * @param \ACP3\Modules\ACP3\Permissions\Model     $permissionsModel
     * @param \ACP3\Modules\ACP3\Permissions\Cache     $permissionsCache
     * @param \ACP3\Modules\ACP3\Permissions\Validator $permissionsValidator
     */
    public function __construct(
        Core\Context\Admin $context,
        Core\Helpers\Secure $secureHelper,
        Permissions\Model $permissionsModel,
        Permissions\Cache $permissionsCache,
        Permissions\Validator $permissionsValidator)
    {
        parent::__construct($context);

        $this->secureHelper = $secureHelper;
        $this->permissionsModel = $permissionsModel;
        $this->permissionsCache = $permissionsCache;
        $this->permissionsValidator = $permissionsValidator;
    }

    public function actionCreate()
    {
        if (empty($_POST) === false) {
            $this->_createPost($_POST);
        }

        $modules = $this->modules->getActiveModules();
        foreach ($modules as $row) {
            $modules[$row['name']]['selected'] = $this->get('core.helpers.forms')->selectEntry('modules', $row['name']);
        }
        $this->view->assign('modules', $modules);

        $privileges = $this->acl->getAllPrivileges();
        $c_privileges = count($privileges);
        for ($i = 0; $i < $c_privileges; ++$i) {
            $privileges[$i]['selected'] = $this->get('core.helpers.forms')->selectEntry('privileges', $privileges[$i]['id']);
        }
        $this->view->assign('privileges', $privileges);

        $this->view->assign('form', array_merge(['resource' => '', 'area' => '', 'controller' => ''], $_POST));

        $this->secureHelper->generateFormToken($this->request->query);
    }

    public function actionDelete()
    {
        $items = $this->_deleteItem();

        if ($this->request->action === 'confirmed') {
            $bool = false;

            foreach ($items as $item) {
                $bool = $this->permissionsModel->delete($item, Permissions\Model::TABLE_NAME_RESOURCES);
            }

            $this->permissionsCache->setResourcesCache();

            $this->redirectMessages()->setMessage($bool, $this->lang->t('system', $bool !== false ? 'delete_success' : 'delete_error'));
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
                $privileges[$i]['selected'] = $this->get('core.helpers.forms')->selectEntry('privileges', $privileges[$i]['id'], $resource['privilege_id']);
            }
            $this->view->assign('privileges', $privileges);

            $defaults = [
                'resource' => $resource['page'],
                'area' => $resource['area'],
                'controller' => $resource['controller'],
                'modules' => $resource['module_name']
            ];
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
        $output = [];
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
    protected function _createPost(array $formData)
    {
        try {
            $this->permissionsValidator->validateResource($formData);

            $moduleInfo = $this->modules->getModuleInfo($formData['modules']);
            $insertValues = [
                'id' => '',
                'module_id' => $moduleInfo['id'],
                'area' => $formData['area'],
                'controller' => $formData['controller'],
                'page' => $formData['resource'],
                'params' => '',
                'privilege_id' => $formData['privileges'],
            ];
            $bool = $this->permissionsModel->insert($insertValues, Permissions\Model::TABLE_NAME_RESOURCES);

            $this->permissionsCache->setResourcesCache();

            $this->secureHelper->unsetFormToken($this->request->query);

            $this->redirectMessages()->setMessage($bool, $this->lang->t('system', $bool !== false ? 'create_success' : 'create_error'));
        } catch (Core\Exceptions\InvalidFormToken $e) {
            $this->redirectMessages()->setMessage(false, $e->getMessage());
        } catch (Core\Exceptions\ValidationFailed $e) {
            $this->view->assign('error_msg', $this->get('core.helpers.alerts')->errorBox($e->getMessage()));
        }
    }

    /**
     * @param array $formData
     */
    protected function _editPost(array $formData)
    {
        try {
            $this->permissionsValidator->validateResource($formData);

            $updateValues = [
                'controller' => $formData['controller'],
                'area' => $formData['area'],
                'page' => $formData['resource'],
                'privilege_id' => $formData['privileges'],
            ];
            $bool = $this->permissionsModel->update($updateValues, $this->request->id, Permissions\Model::TABLE_NAME_RESOURCES);

            $this->permissionsCache->setResourcesCache();

            $this->secureHelper->unsetFormToken($this->request->query);

            $this->redirectMessages()->setMessage($bool, $this->lang->t('system', $bool !== false ? 'edit_success' : 'edit_error'));
        } catch (Core\Exceptions\InvalidFormToken $e) {
            $this->redirectMessages()->setMessage(false, $e->getMessage());
        } catch (Core\Exceptions\ValidationFailed $e) {
            $this->view->assign('error_msg', $this->get('core.helpers.alerts')->errorBox($e->getMessage()));
        }
    }
}
