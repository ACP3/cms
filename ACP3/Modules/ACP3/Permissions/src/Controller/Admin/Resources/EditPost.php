<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Permissions\Controller\Admin\Resources;

use ACP3\Core;
use ACP3\Core\Controller\Context\Context;
use ACP3\Core\Helpers\FormAction;
use ACP3\Modules\ACP3\Permissions;
use ACP3\Modules\ACP3\Permissions\Model\AclResourceModel;
use ACP3\Modules\ACP3\Permissions\Validation\ResourceFormValidation;
use Doctrine\DBAL\ConnectionException;
use Doctrine\DBAL\Exception;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;

class EditPost extends Core\Controller\AbstractWidgetAction
{
    public function __construct(
        Context                        $context,
        private FormAction             $actionHelper,
        private Core\Modules           $modules,
        private AclResourceModel       $resourcesModel,
        private ResourceFormValidation $resourceFormValidation
    ) {
        parent::__construct($context);
    }

    /**
     * @param int $id
     * @return array<string, mixed>|string|Response
     * @throws ConnectionException
     * @throws Exception
     */
    public function __invoke(int $id): array|string|Response
    {
        return $this->actionHelper->handleSaveAction(function () use ($id) {
            $formData = $this->request->getPost()->all();

            $this->resourceFormValidation->validate($formData);

            $formData['module_id'] = $this->modules->getModuleInfo($formData['modules'])['id'] ?? 0;

            return $this->resourcesModel->save($formData, $id);
        });
    }
}
