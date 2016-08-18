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
class Edit extends AbstractFormAction
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
     * @var \ACP3\Modules\ACP3\Permissions\Validation\ResourceFormValidation
     */
    protected $resourceFormValidation;
    /**
     * @var Permissions\Model\ResourcesModel
     */
    protected $resourcesModel;

    /**
     * @param \ACP3\Core\Controller\Context\AdminContext $context
     * @param \ACP3\Core\Helpers\Forms $formsHelper
     * @param \ACP3\Core\Helpers\FormToken $formTokenHelper
     * @param \ACP3\Modules\ACP3\Permissions\Model\Repository\PrivilegeRepository $privilegeRepository
     * @param \ACP3\Modules\ACP3\Permissions\Model\Repository\ResourceRepository $resourceRepository
     * @param Permissions\Model\ResourcesModel $resourcesModel
     * @param \ACP3\Modules\ACP3\Permissions\Validation\ResourceFormValidation $resourceFormValidation
     */
    public function __construct(
        Core\Controller\Context\AdminContext $context,
        Core\Helpers\Forms $formsHelper,
        Core\Helpers\FormToken $formTokenHelper,
        Permissions\Model\Repository\PrivilegeRepository $privilegeRepository,
        Permissions\Model\Repository\ResourceRepository $resourceRepository,
        Permissions\Model\ResourcesModel $resourcesModel,
        Permissions\Validation\ResourceFormValidation $resourceFormValidation
    ) {
        parent::__construct($context, $formsHelper, $privilegeRepository);

        $this->formTokenHelper = $formTokenHelper;
        $this->resourceRepository = $resourceRepository;
        $this->resourceFormValidation = $resourceFormValidation;
        $this->resourcesModel = $resourcesModel;
    }

    /**
     * @param int $id
     *
     * @return array|\Symfony\Component\HttpFoundation\RedirectResponse
     * @throws \ACP3\Core\Controller\Exception\ResultNotExistsException
     */
    public function execute($id)
    {
        $resource = $this->resourceRepository->getResourceById($id);
        if (!empty($resource)) {
            if ($this->request->getPost()->count() !== 0) {
                return $this->executePost($this->request->getPost()->all(), $id);
            }

            $defaults = [
                'resource' => $resource['page'],
                'area' => $resource['area'],
                'controller' => $resource['controller']
            ];

            return [
                'modules' => $this->fetchActiveModules($resource['module_name']),
                'privileges' => $this->fetchPrivileges($resource['privilege_id']),
                'form' => array_merge($defaults, $this->request->getPost()->all()),
                'form_token' => $this->formTokenHelper->renderFormToken()
            ];
        }

        throw new Core\Controller\Exception\ResultNotExistsException();
    }

    /**
     * @param array $formData
     * @param int   $resourceId
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    protected function executePost(array $formData, $resourceId)
    {
        return $this->actionHelper->handleEditPostAction(function () use ($formData, $resourceId) {
            $this->resourceFormValidation->validate($formData);

            $formData['module_id'] = $this->fetchModuleId($formData['modules']);
            return $this->resourcesModel->saveResource($formData, $resourceId);
        });
    }
}
