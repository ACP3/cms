<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Users\Controller\Frontend\Account;

use ACP3\Core;
use ACP3\Modules\ACP3\Users;

class Settings extends AbstractAction
{
    /**
     * @var \ACP3\Core\Helpers\FormToken
     */
    protected $formTokenHelper;
    /**
     * @var \ACP3\Modules\ACP3\Users\Validation\AccountSettingsFormValidation
     */
    protected $accountSettingsFormValidation;
    /**
     * @var \ACP3\Modules\ACP3\Users\Helpers\Forms
     */
    protected $userFormsHelper;
    /**
     * @var Users\Model\UsersModel
     */
    protected $usersModel;
    /**
     * @var Core\Helpers\Secure
     */
    protected $secureHelper;
    /**
     * @var Users\Model\AuthenticationModel
     */
    protected $authenticationModel;

    public function __construct(
        Core\Controller\Context\FormContext $context,
        Core\Helpers\FormToken $formTokenHelper,
        Core\Helpers\Secure $secureHelper,
        Users\Helpers\Forms $userFormsHelper,
        Users\Model\AuthenticationModel $authenticationModel,
        Users\Model\UsersModel $usersModel,
        Users\Validation\AccountSettingsFormValidation $accountSettingsFormValidation
    ) {
        parent::__construct($context);

        $this->formTokenHelper = $formTokenHelper;
        $this->userFormsHelper = $userFormsHelper;
        $this->accountSettingsFormValidation = $accountSettingsFormValidation;
        $this->usersModel = $usersModel;
        $this->secureHelper = $secureHelper;
        $this->authenticationModel = $authenticationModel;
    }

    /**
     * @return array
     *
     * @throws \Doctrine\DBAL\DBALException
     */
    public function execute()
    {
        $user = $this->usersModel->getOneById($this->user->getUserId());

        $this->view->assign(
            $this->userFormsHelper->fetchUserSettingsFormFields(
                $user['address_display'],
                $user['birthday_display'],
                $user['country_display'],
                $user['mail_display']
            )
        );

        return [
            'form' => \array_merge($user, $this->request->getPost()->all()),
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
        return $this->actionHelper->handlePostAction(
            function () {
                $formData = $this->request->getPost()->all();

                $this->accountSettingsFormValidation->validate($formData);

                if (!empty($formData['new_pwd']) && !empty($formData['new_pwd_repeat'])) {
                    $salt = $this->secureHelper->salt(Users\Model\UserModel::SALT_LENGTH);
                    $newPassword = $this->secureHelper->generateSaltedPassword($salt, $formData['new_pwd'], 'sha512');
                    $formData['pwd'] = $newPassword;
                    $formData['pwd_salt'] = $salt;
                }

                $bool = $this->usersModel->save($formData, $this->user->getUserId());

                $user = $this->usersModel->getOneById($this->user->getUserId());
                $cookie = $this->authenticationModel->setRememberMeCookie(
                    $this->user->getUserId(),
                    $user['remember_me_token']
                );
                $this->response->headers->setCookie($cookie);

                return $this->redirectMessages()->setMessage(
                    $bool,
                    $this->translator->t('system', $bool !== false ? 'settings_success' : 'settings_error')
                );
            }
        );
    }
}
