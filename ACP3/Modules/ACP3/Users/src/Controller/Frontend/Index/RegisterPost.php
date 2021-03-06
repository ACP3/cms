<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Users\Controller\Frontend\Index;

use ACP3\Core;
use ACP3\Core\Modules\Helper\Action;
use ACP3\Modules\ACP3\Permissions;
use ACP3\Modules\ACP3\Users;

class RegisterPost extends Core\Controller\AbstractWidgetAction implements Core\Controller\InvokableActionInterface
{
    /**
     * @var \ACP3\Modules\ACP3\Users\Validation\RegistrationFormValidation
     */
    private $registrationFormValidation;
    /**
     * @var \ACP3\Modules\ACP3\Permissions\Helpers
     */
    private $permissionsHelpers;
    /**
     * @var \ACP3\Core\Helpers\Alerts
     */
    private $alertsHelper;
    /**
     * @var \ACP3\Modules\ACP3\Users\Model\UsersModel
     */
    private $usersModel;
    /**
     * @var \ACP3\Core\Modules\Helper\Action
     */
    private $actionHelper;

    public function __construct(
        Core\Controller\Context\WidgetContext $context,
        Action $actionHelper,
        Core\Helpers\Alerts $alertsHelper,
        Users\Model\UsersModel $usersModel,
        Users\Validation\RegistrationFormValidation $registrationFormValidation,
        Permissions\Helpers $permissionsHelpers
    ) {
        parent::__construct($context);

        $this->registrationFormValidation = $registrationFormValidation;
        $this->permissionsHelpers = $permissionsHelpers;
        $this->alertsHelper = $alertsHelper;
        $this->usersModel = $usersModel;
        $this->actionHelper = $actionHelper;
    }

    /**
     * @return array|string|\Symfony\Component\HttpFoundation\JsonResponse|\Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     *
     * @throws \Doctrine\DBAL\ConnectionException
     * @throws \Doctrine\DBAL\Exception
     */
    public function __invoke()
    {
        return $this->actionHelper->handlePostAction(
            function () {
                $formData = $this->request->getPost()->all();

                $this->registrationFormValidation->validate($formData);

                $insertValues = [
                    'nickname' => $formData['nickname'],
                    'pwd' => $formData['pwd'],
                    'mail' => $formData['mail'],
                ];

                $lastId = $this->usersModel->save($insertValues);

                $result = $this->permissionsHelpers->updateUserRoles([2], $lastId);

                return $this->alertsHelper->confirmBox(
                    $this->translator->t(
                        'users',
                        $lastId !== false && $result !== false ? 'register_success' : 'register_error'
                    ),
                    $this->appPath->getWebRoot()
                );
            },
            $this->request->getFullPath()
        );
    }
}
