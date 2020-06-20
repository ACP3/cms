<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Permissions\Controller\Admin\Resources;

use ACP3\Core;
use ACP3\Core\Modules\Helper\Action;
use ACP3\Modules\ACP3\Permissions;

class Create extends AbstractFormAction
{
    /**
     * @var \ACP3\Modules\ACP3\Permissions\Validation\ResourceFormValidation
     */
    private $resourceFormValidation;
    /**
     * @var Permissions\Model\ResourcesModel
     */
    private $resourcesModel;
    /**
     * @var \ACP3\Modules\ACP3\Permissions\ViewProviders\AdminResourceEditViewProvider
     */
    private $adminResourceEditViewProvider;
    /**
     * @var \ACP3\Core\Modules\Helper\Action
     */
    private $actionHelper;

    public function __construct(
        Core\Controller\Context\FrontendContext $context,
        Action $actionHelper,
        Core\Modules $modules,
        Permissions\Model\ResourcesModel $resourcesModel,
        Permissions\Validation\ResourceFormValidation $resourceFormValidation,
        Permissions\ViewProviders\AdminResourceEditViewProvider $adminResourceEditViewProvider
    ) {
        parent::__construct($context, $modules);

        $this->resourceFormValidation = $resourceFormValidation;
        $this->resourcesModel = $resourcesModel;
        $this->adminResourceEditViewProvider = $adminResourceEditViewProvider;
        $this->actionHelper = $actionHelper;
    }

    /**
     * @throws \ReflectionException
     */
    public function execute(): array
    {
        $defaults = [
            'page' => '',
            'area' => '',
            'controller' => '',
            'module_name' => null,
            'privilege_id' => 0,
        ];

        return ($this->adminResourceEditViewProvider)($defaults);
    }

    /**
     * @return array|string|\Symfony\Component\HttpFoundation\JsonResponse|\Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     *
     * @throws \Doctrine\DBAL\ConnectionException
     * @throws \Doctrine\DBAL\DBALException
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
