<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Permissions\Controller\Admin\Resources;

use ACP3\Core;
use ACP3\Modules\ACP3\Permissions;

class Create extends AbstractFormAction
{
    /**
     * @var \ACP3\Core\Helpers\FormToken
     */
    protected $formTokenHelper;
    /**
     * @var \ACP3\Modules\ACP3\Permissions\Validation\ResourceFormValidation
     */
    protected $resourceFormValidation;
    /**
     * @var Permissions\Model\ResourcesModel
     */
    protected $resourcesModel;

    public function __construct(
        Core\Controller\Context\FormContext $context,
        Core\Helpers\Forms $formsHelper,
        Core\Helpers\FormToken $formTokenHelper,
        Permissions\Model\Repository\PrivilegeRepository $privilegeRepository,
        Permissions\Model\ResourcesModel $resourcesModel,
        Permissions\Validation\ResourceFormValidation $resourceFormValidation
    ) {
        parent::__construct($context, $formsHelper, $privilegeRepository);

        $this->formTokenHelper = $formTokenHelper;
        $this->resourceFormValidation = $resourceFormValidation;
        $this->resourcesModel = $resourcesModel;
    }

    /**
     * @return array
     */
    public function execute()
    {
        return [
            'modules' => $this->fetchActiveModules(),
            'areas' => $this->fetchAreas(),
            'privileges' => $this->fetchPrivileges(0),
            'form' => \array_merge(
                ['resource' => '', 'area' => '', 'controller' => ''],
                $this->request->getPost()->all()
            ),
            'form_token' => $this->formTokenHelper->renderFormToken(),
        ];
    }

    /**
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     *
     * @throws \Doctrine\DBAL\ConnectionException
     */
    public function executePost()
    {
        return $this->actionHelper->handleSaveAction(function () {
            $formData = $this->request->getPost()->all();

            $this->resourceFormValidation->validate($formData);

            $formData['module_id'] = $this->fetchModuleId($formData['modules']);

            return $this->resourcesModel->save($formData);
        });
    }
}
