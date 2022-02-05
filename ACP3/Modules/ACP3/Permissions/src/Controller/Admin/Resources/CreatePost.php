<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Permissions\Controller\Admin\Resources;

use ACP3\Core;
use ACP3\Core\Helpers\FormAction;
use ACP3\Modules\ACP3\Permissions;
use Doctrine\DBAL\ConnectionException;
use Doctrine\DBAL\Exception;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;

class CreatePost extends Core\Controller\AbstractWidgetAction
{
    public function __construct(
        Core\Controller\Context\Context                       $context,
        private FormAction                                    $actionHelper,
        private Core\Modules                                  $modules,
        private Permissions\Model\AclResourceModel            $resourcesModel,
        private Permissions\Validation\ResourceFormValidation $resourceFormValidation
    ) {
        parent::__construct($context);
    }

    /**
     * @return array<string, mixed>|string|Response
     * @throws ConnectionException
     * @throws Exception
     */
    public function __invoke(): array|string|Response
    {
        return $this->actionHelper->handleSaveAction(function () {
            $formData = $this->request->getPost()->all();

            $this->resourceFormValidation->validate($formData);

            $formData['module_id'] = $this->modules->getModuleInfo($formData['modules'])['id'] ?? 0;

            return $this->resourcesModel->save($formData);
        });
    }
}
