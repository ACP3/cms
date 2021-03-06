<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Users\Controller\Frontend\Index;

use ACP3\Core;
use ACP3\Core\Modules\Helper\Action;
use ACP3\Core\Validation\ValidationRules\EmailValidationRule;
use ACP3\Modules\ACP3\Users;

class ForgotPwdPost extends Core\Controller\AbstractWidgetAction implements Core\Controller\InvokableActionInterface
{
    /**
     * @var \ACP3\Core\Helpers\Secure
     */
    private $secureHelper;
    /**
     * @var \ACP3\Modules\ACP3\Users\Model\Repository\UserRepository
     */
    private $userRepository;
    /**
     * @var \ACP3\Modules\ACP3\Users\Validation\AccountForgotPasswordFormValidation
     */
    private $accountForgotPasswordFormValidation;
    /**
     * @var \ACP3\Core\Helpers\Alerts
     */
    private $alertsHelper;
    /**
     * @var \ACP3\Core\Validation\Validator
     */
    private $validator;
    /**
     * @var \ACP3\Modules\ACP3\Users\Model\UsersModel
     */
    private $usersModel;
    /**
     * @var \ACP3\Modules\ACP3\Users\Helpers\SendPasswordChangeEmail
     */
    private $sendPasswordChangeEmail;
    /**
     * @var \ACP3\Core\Modules\Helper\Action
     */
    private $actionHelper;

    public function __construct(
        Core\Controller\Context\WidgetContext $context,
        Action $actionHelper,
        Core\Validation\Validator $validator,
        Core\Helpers\Alerts $alertsHelper,
        Core\Helpers\Secure $secureHelper,
        Users\Model\UsersModel $usersModel,
        Users\Model\Repository\UserRepository $userRepository,
        Users\Validation\AccountForgotPasswordFormValidation $accountForgotPasswordFormValidation,
        Users\Helpers\SendPasswordChangeEmail $sendPasswordChangeEmail
    ) {
        parent::__construct($context);

        $this->secureHelper = $secureHelper;
        $this->userRepository = $userRepository;
        $this->accountForgotPasswordFormValidation = $accountForgotPasswordFormValidation;
        $this->alertsHelper = $alertsHelper;
        $this->validator = $validator;
        $this->usersModel = $usersModel;
        $this->sendPasswordChangeEmail = $sendPasswordChangeEmail;
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

                $this->accountForgotPasswordFormValidation->validate($formData);

                $newPassword = $this->secureHelper->salt(Users\Model\UserModel::SALT_LENGTH);
                $user = $this->fetchUserByFormFieldValue($formData['nick_mail']);
                $isMailSent = ($this->sendPasswordChangeEmail)($user, $newPassword);
                $result = false;

                // Das Passwort des Benutzers nur abändern, wenn die E-Mail erfolgreich versendet werden konnte
                if ($isMailSent === true) {
                    $updateValues = [
                        'pwd' => $newPassword,
                        'login_errors' => 0,
                    ];
                    $result = $this->usersModel->save($updateValues, $user['id']);
                }

                return $this->alertsHelper->confirmBox(
                    $this->translator->t(
                        'users',
                        $isMailSent === true && $result !== false ? 'forgot_pwd_success' : 'forgot_pwd_error'
                    ),
                    $this->appPath->getWebRoot()
                );
            },
            $this->request->getFullPath()
        );
    }

    /**
     * @throws \Doctrine\DBAL\Exception
     * @throws \ACP3\Core\Validation\Exceptions\ValidationRuleNotFoundException
     */
    protected function fetchUserByFormFieldValue(string $nickNameOrEmail): array
    {
        if ($this->validator->is(EmailValidationRule::class, $nickNameOrEmail) === true &&
            $this->userRepository->resultExistsByEmail($nickNameOrEmail) === true
        ) {
            $user = $this->userRepository->getOneByEmail($nickNameOrEmail);
        } else {
            $user = $this->userRepository->getOneByNickname($nickNameOrEmail);
        }

        return $user;
    }
}
