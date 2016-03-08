<?php
/**
 * Copyright (c) 2016 by the ACP3 Developers.
 * See the LICENCE file at the top-level module directory for licencing details.
 */

namespace ACP3\Modules\ACP3\Permissions\Controller\Admin\Resources;

use ACP3\Core;
use ACP3\Modules\ACP3\Permissions;

/**
 * Class Edit
 * @package ACP3\Modules\ACP3\Permissions\Controller\Admin\Resources
 */
class Edit extends Core\Controller\AdminAction
{
    /**
     * @var \ACP3\Core\Helpers\FormToken
     */
    protected $formTokenHelper;
    /**
     * @var \ACP3\Modules\ACP3\Permissions\Model\ResourceRepository
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
     * @var \ACP3\Core\Helpers\Forms
     */
    protected $formsHelper;

    /**
     * @param \ACP3\Core\Controller\Context\AdminContext                       $context
     * @param \ACP3\Core\Helpers\Forms                                         $formsHelper
     * @param \ACP3\Core\Helpers\FormToken                                     $formTokenHelper
     * @param \ACP3\Modules\ACP3\Permissions\Model\ResourceRepository          $resourceRepository
     * @param \ACP3\Modules\ACP3\Permissions\Cache                             $permissionsCache
     * @param \ACP3\Modules\ACP3\Permissions\Validation\ResourceFormValidation $resourceFormValidation
     */
    public function __construct(
        Core\Controller\Context\AdminContext $context,
        Core\Helpers\Forms $formsHelper,
        Core\Helpers\FormToken $formTokenHelper,
        Permissions\Model\ResourceRepository $resourceRepository,
        Permissions\Cache $permissionsCache,
        Permissions\Validation\ResourceFormValidation $resourceFormValidation
    ) {
        parent::__construct($context);

        $this->formsHelper = $formsHelper;
        $this->formTokenHelper = $formTokenHelper;
        $this->resourceRepository = $resourceRepository;
        $this->permissionsCache = $permissionsCache;
        $this->resourceFormValidation = $resourceFormValidation;
    }

    /**
     * @param int $id
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     * @throws \ACP3\Core\Exceptions\ResultNotExists
     */
    public function execute($id)
    {
        $resource = $this->resourceRepository->getResourceById($id);
        if (!empty($resource)) {
            if ($this->request->getPost()->isEmpty() === false) {
                return $this->executePost($this->request->getPost()->all(), $id);
            }

            $privileges = $this->acl->getAllPrivileges();
            $cPrivileges = count($privileges);
            for ($i = 0; $i < $cPrivileges; ++$i) {
                $privileges[$i]['selected'] = $this->formsHelper->selectEntry(
                    'privileges',
                    $privileges[$i]['id'],
                    $resource['privilege_id']
                );
            }

            $defaults = [
                'resource' => $resource['page'],
                'area' => $resource['area'],
                'controller' => $resource['controller'],
                'modules' => $resource['module_name']
            ];

            return [
                'privileges' => $privileges,
                'form' => array_merge($defaults, $this->request->getPost()->all()),
                'form_token' => $this->formTokenHelper->renderFormToken()
            ];
        }

        throw new Core\Exceptions\ResultNotExists();
    }

    /**
     * @param array $formData
     * @param int   $id
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    protected function executePost(array $formData, $id)
    {
        return $this->actionHelper->handleEditPostAction(function () use ($formData, $id) {
            $this->resourceFormValidation->validate($formData);

            $updateValues = [
                'controller' => $formData['controller'],
                'area' => $formData['area'],
                'page' => $formData['resource'],
                'privilege_id' => $formData['privileges'],
            ];
            $bool = $this->resourceRepository->update($updateValues, $id);

            $this->permissionsCache->saveResourcesCache();

            $this->formTokenHelper->unsetFormToken();

            return $bool;
        });
    }
}
