<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Users\Controller\Frontend\Index;

use ACP3\Core;
use ACP3\Core\Authentication\Model\UserModelInterface;
use ACP3\Core\Validation\ValidationRules\EmailValidationRule;
use ACP3\Modules\ACP3\Users;

class ForgotPwd extends Core\Controller\AbstractFrontendAction
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
     * @var \ACP3\Core\Http\RedirectResponse
     */
    private $redirectResponse;
    /**
     * @var \ACP3\Modules\ACP3\Users\ViewProviders\ForgotPasswordViewProvider
     */
    private $forgotPasswordViewProvider;
    /**
     * @var \ACP3\Modules\ACP3\Users\Model\UsersModel
     */
    private $usersModel;
    /**
     * @var \ACP3\Modules\ACP3\Users\Helpers\SendPasswordChangeEmail
     */
    private $sendPasswordChangeEmail;
    /**
     * @var \ACP3\Core\Authentication\Model\UserModelInterface
     */
    private $user;

    public function __construct(
        Core\Controller\Context\FrontendContext $context,
        UserModelInterface $user,
        Core\Http\RedirectResponse $redirectResponse,
        Core\Validation\Validator $validator,
        Core\Helpers\Alerts $alertsHelper,
        Core\Helpers\Secure $secureHelper,
        Users\ViewProviders\ForgotPasswordViewProvider $forgotPasswordViewProvider,
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
        $this->redirectResponse = $redirectResponse;
        $this->forgotPasswordViewProvider = $forgotPasswordViewProvider;
        $this->usersModel = $usersModel;
        $this->sendPasswordChangeEmail = $sendPasswordChangeEmail;
        $this->user = $user;
    }

    /**
     * @return array|\Symfony\Component\HttpFoundation\JsonResponse|\Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function execute()
    {
        if ($this->user->isAuthenticated() === true) {
            return $this->redirectResponse->toNewPage($this->appPath->getWebRoot());
        }

        return ($this->forgotPasswordViewProvider)();
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

                $this->accountForgotPasswordFormValidation->validate($formData);

                $newPassword = $this->secureHelper->salt(Users\Model\UserModel::SALT_LENGTH);
                $user = $this->fetchUserByFormFieldValue($formData['nick_mail']);
                $isMailSent = ($this->sendPasswordChangeEmail)($user, $newPassword);

                // Das Passwort des Benutzers nur abändern, wenn die E-Mail erfolgreich versendet werden konnte
                if ($isMailSent === true) {
                    $updateValues = [
                        'pwd' => $newPassword,
                        'login_errors' => 0,
                    ];
                    $bool = $this->usersModel->save($updateValues, $user['id']);
                }

                $this->setTemplate($this->alertsHelper->confirmBox(
                    $this->translator->t(
                        'users',
                        $isMailSent === true && isset($bool) && $bool !== false ? 'forgot_pwd_success' : 'forgot_pwd_error'
                    ),
                    $this->appPath->getWebRoot()
                ));
            },
            $this->request->getFullPath()
        );
    }

    /**
     * @throws \Doctrine\DBAL\DBALException
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
