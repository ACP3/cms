<?php
/**
 * Copyright (c) 2016 by the ACP3 Developers. See the LICENCE file at the top-level module directory for licencing details.
 */

namespace ACP3\Modules\ACP3\Users\Controller\Frontend\Account;

use ACP3\Core;
use ACP3\Modules\ACP3\Users;

/**
 * Class Settings
 * @package ACP3\Modules\ACP3\Users\Controller\Frontend\Account
 */
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

    /**
     * Settings constructor.
     *
     * @param \ACP3\Core\Controller\Context\FrontendContext $context
     * @param \ACP3\Core\Helpers\FormToken $formTokenHelper
     * @param Core\Helpers\Secure $secureHelper
     * @param \ACP3\Modules\ACP3\Users\Helpers\Forms $userFormsHelper
     * @param Users\Model\AuthenticationModel $authenticationModel
     * @param Users\Model\UsersModel $usersModel
     * @param \ACP3\Modules\ACP3\Users\Validation\AccountSettingsFormValidation $accountSettingsFormValidation
     */
    public function __construct(
        Core\Controller\Context\FrontendContext $context,
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
            'form' => array_merge($user, $this->request->getPost()->all()),
            'form_token' => $this->formTokenHelper->renderFormToken()
        ];
    }

    /**
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
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
