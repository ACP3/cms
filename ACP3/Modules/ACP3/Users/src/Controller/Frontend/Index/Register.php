<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Users\Controller\Frontend\Index;

use ACP3\Core;
use ACP3\Modules\ACP3\Permissions;
use ACP3\Modules\ACP3\Users;

class Register extends Core\Controller\AbstractFrontendAction
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
     * @var \ACP3\Core\Http\RedirectResponse
     */
    private $redirectResponse;
    /**
     * @var \ACP3\Modules\ACP3\Users\ViewProviders\RegistrationViewProvider
     */
    private $registrationViewProvider;
    /**
     * @var \ACP3\Modules\ACP3\Users\Model\UsersModel
     */
    private $usersModel;

    public function __construct(
        Core\Controller\Context\FrontendContext $context,
        Core\Http\RedirectResponse $redirectResponse,
        Core\Helpers\Alerts $alertsHelper,
        Users\ViewProviders\RegistrationViewProvider $registrationViewProvider,
        Users\Model\UsersModel $usersModel,
        Users\Validation\RegistrationFormValidation $registrationFormValidation,
        Permissions\Helpers $permissionsHelpers
    ) {
        parent::__construct($context);

        $this->registrationFormValidation = $registrationFormValidation;
        $this->permissionsHelpers = $permissionsHelpers;
        $this->alertsHelper = $alertsHelper;
        $this->redirectResponse = $redirectResponse;
        $this->registrationViewProvider = $registrationViewProvider;
        $this->usersModel = $usersModel;
    }

    /**
     * @return array|\Symfony\Component\HttpFoundation\JsonResponse|\Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function execute()
    {
        if ($this->user->isAuthenticated() === true) {
            return $this->redirectResponse->toNewPage($this->appPath->getWebRoot());
        }

        $settings = $this->config->getSettings(Users\Installer\Schema::MODULE_NAME);

        if ($settings['enable_registration'] == 0) {
            $this->setContent(
                $this->alertsHelper->errorBox(
                    $this->translator->t('users', 'user_registration_disabled')
                )
            );
        }

        return ($this->registrationViewProvider)();
    }

    /**
     * @return array|string|\Symfony\Component\HttpFoundation\JsonResponse|\Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     *
     * @throws \Doctrine\DBAL\ConnectionException
     * @throws \Doctrine\DBAL\DBALException
     */
    public function executePost()
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

                $bool2 = $this->permissionsHelpers->updateUserRoles([2], $lastId);

                $this->setTemplate($this->alertsHelper->confirmBox(
                    $this->translator->t(
                        'users',
                        $lastId !== false && $bool2 !== false ? 'register_success' : 'register_error'
                    ),
                    $this->appPath->getWebRoot()
                ));
            },
            $this->request->getFullPath()
        );
    }
}
