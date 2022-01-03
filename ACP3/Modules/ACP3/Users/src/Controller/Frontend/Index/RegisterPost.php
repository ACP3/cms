<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Users\Controller\Frontend\Index;

use ACP3\Core;
use ACP3\Core\Environment\ApplicationPath;
use ACP3\Core\Helpers\FormAction;
use ACP3\Modules\ACP3\Permissions;
use ACP3\Modules\ACP3\Users;

class RegisterPost extends Core\Controller\AbstractWidgetAction
{
    public function __construct(
        Core\Controller\Context\WidgetContext $context,
        private ApplicationPath $applicationPath,
        private FormAction $actionHelper,
        private Core\Helpers\Alerts $alertsHelper,
        private Users\Model\UsersModel $usersModel,
        private Users\Validation\RegistrationFormValidation $registrationFormValidation,
        private Permissions\Helpers $permissionsHelpers
    ) {
        parent::__construct($context);
    }

    /**
     * @throws \Doctrine\DBAL\ConnectionException
     * @throws \Doctrine\DBAL\Exception
     */
    public function __invoke(): array|string|\Symfony\Component\HttpFoundation\JsonResponse|\Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
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
                    $this->applicationPath->getWebRoot()
                );
            },
            $this->request->getFullPath()
        );
    }
}
