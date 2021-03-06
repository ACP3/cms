<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Permissions\Controller\Admin\Index;

use ACP3\Core;
use ACP3\Core\Modules\Helper\Action;
use ACP3\Modules\ACP3\Permissions;

class EditPost extends Core\Controller\AbstractWidgetAction implements Core\Controller\InvokableActionInterface
{
    /**
     * @var \ACP3\Modules\ACP3\Permissions\Validation\RoleFormValidation
     */
    private $roleFormValidation;
    /**
     * @var Permissions\Model\RolesModel
     */
    private $rolesModel;
    /**
     * @var Permissions\Model\RulesModel
     */
    private $rulesModel;
    /**
     * @var \ACP3\Core\Modules\Helper\Action
     */
    private $actionHelper;

    public function __construct(
        Core\Controller\Context\WidgetContext $context,
        Action $actionHelper,
        Permissions\Model\RolesModel $rolesModel,
        Permissions\Model\RulesModel $rulesModel,
        Permissions\Validation\RoleFormValidation $roleFormValidation
    ) {
        parent::__construct($context);

        $this->roleFormValidation = $roleFormValidation;
        $this->rolesModel = $rolesModel;
        $this->rulesModel = $rulesModel;
        $this->actionHelper = $actionHelper;
    }

    /**
     * @return array|string|\Symfony\Component\HttpFoundation\JsonResponse|\Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     *
     * @throws \Doctrine\DBAL\ConnectionException
     * @throws \Doctrine\DBAL\Exception
     */
    public function __invoke(int $id)
    {
        return $this->actionHelper->handleSaveAction(function () use ($id) {
            $formData = $this->request->getPost()->all();

            $this->roleFormValidation
                ->setRoleId($id)
                ->validate($formData);

            $formData['parent_id'] = $id === 1 ? 0 : $formData['parent_id'];

            $result = $this->rolesModel->save($formData, $id);
            $this->rulesModel->updateRules($formData['privileges'], $id);

            return $result;
        });
    }
}
