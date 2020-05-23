<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Users\Controller\Admin\Index;

use ACP3\Core;
use ACP3\Modules\ACP3\Permissions;
use ACP3\Modules\ACP3\Users;

class Create extends Core\Controller\AbstractFrontendAction
{
    /**
     * @var \ACP3\Modules\ACP3\Users\Validation\AdminFormValidation
     */
    private $adminFormValidation;
    /**
     * @var \ACP3\Modules\ACP3\Permissions\Helpers
     */
    private $permissionsHelpers;
    /**
     * @var Users\Model\UsersModel
     */
    private $usersModel;
    /**
     * @var \ACP3\Modules\ACP3\Users\ViewProviders\AdminUserEditViewProvider
     */
    private $adminUserEditViewProvider;

    public function __construct(
        Core\Controller\Context\FrontendContext $context,
        Users\ViewProviders\AdminUserEditViewProvider $adminUserEditViewProvider,
        Users\Model\UsersModel $usersModel,
        Users\Validation\AdminFormValidation $adminFormValidation,
        Permissions\Helpers $permissionsHelpers
    ) {
        parent::__construct($context);

        $this->adminFormValidation = $adminFormValidation;
        $this->permissionsHelpers = $permissionsHelpers;
        $this->usersModel = $usersModel;
        $this->adminUserEditViewProvider = $adminUserEditViewProvider;
    }

    public function execute(): array
    {
        $defaults = [
            'nickname' => '',
            'realname' => '',
            'street' => '',
            'house_number' => '',
            'zip' => '',
            'city' => '',
        ];

        return ($this->adminUserEditViewProvider)($defaults);
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

            $this->adminFormValidation->validate($formData);

            $lastId = $this->usersModel->save($formData);

            $this->permissionsHelpers->updateUserRoles($formData['roles'], $lastId);

            return $lastId;
        });
    }
}
