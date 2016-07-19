<?php
/**
 * Copyright (c) 2016 by the ACP3 Developers.
 * See the LICENCE file at the top-level module directory for licencing details.
 */

namespace ACP3\Modules\ACP3\Permissions\Controller\Admin\Resources;

use ACP3\Core;
use ACP3\Modules\ACP3\Permissions;

/**
 * Class Create
 * @package ACP3\Modules\ACP3\Permissions\Controller\Admin\Resources
 */
class Create extends AbstractFormAction
{
    /**
     * @var \ACP3\Core\Helpers\FormToken
     */
    protected $formTokenHelper;
    /**
     * @var \ACP3\Modules\ACP3\Permissions\Model\Repository\ResourceRepository
     */
    protected $resourceRepository;
    /**
     * @var \ACP3\Modules\ACP3\Permissions\Cache
     */
    protected $permissionsCache;
    /**
     * @var \ACP3\Modules\ACP3\Permissions\Validation\ResourceFormValidation
     */
    protected $resourceFormValidation;

    /**
     * @param \ACP3\Core\Controller\Context\AdminContext                       $context
     * @param \ACP3\Core\Helpers\Forms                                         $formsHelper
     * @param \ACP3\Core\Helpers\FormToken                                     $formTokenHelper
     * @param \ACP3\Modules\ACP3\Permissions\Model\Repository\PrivilegeRepository         $privilegeRepository
     * @param \ACP3\Modules\ACP3\Permissions\Model\Repository\ResourceRepository          $resourceRepository
     * @param \ACP3\Modules\ACP3\Permissions\Cache                             $permissionsCache
     * @param \ACP3\Modules\ACP3\Permissions\Validation\ResourceFormValidation $resourceFormValidation
     */
    public function __construct(
        Core\Controller\Context\AdminContext $context,
        Core\Helpers\Forms $formsHelper,
        Core\Helpers\FormToken $formTokenHelper,
        Permissions\Model\Repository\PrivilegeRepository $privilegeRepository,
        Permissions\Model\Repository\ResourceRepository $resourceRepository,
        Permissions\Cache $permissionsCache,
        Permissions\Validation\ResourceFormValidation $resourceFormValidation
    ) {
        parent::__construct($context, $formsHelper, $privilegeRepository);

        $this->formTokenHelper = $formTokenHelper;
        $this->resourceRepository = $resourceRepository;
        $this->permissionsCache = $permissionsCache;
        $this->resourceFormValidation = $resourceFormValidation;
    }

    /**
     * @return array|\Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function execute()
    {
        if ($this->request->getPost()->count() !== 0) {
            return $this->executePost($this->request->getPost()->all());
        }

        $modules = $this->modules->getActiveModules();
        foreach ($modules as $row) {
            $modules[$row['name']]['selected'] = $this->formsHelper->selectEntry('modules', $row['name']);
        }

        return [
            'modules' => $modules,
            'privileges' => $this->fetchPrivileges(0),
            'form' => array_merge(
                ['resource' => '', 'area' => '', 'controller' => ''],
                $this->request->getPost()->all()
            ),
            'form_token' => $this->formTokenHelper->renderFormToken()
        ];
    }

    /**
     * @param array $formData
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    protected function executePost(array $formData)
    {
        return $this->actionHelper->handleCreatePostAction(function () use ($formData) {
            $this->resourceFormValidation->validate($formData);

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
            $bool = $this->resourceRepository->insert($insertValues);

            $this->permissionsCache->saveResourcesCache();

            Core\Cache\Purge::doPurge($this->appPath->getCacheDir() . 'http');

            return $bool;
        });
    }
}
