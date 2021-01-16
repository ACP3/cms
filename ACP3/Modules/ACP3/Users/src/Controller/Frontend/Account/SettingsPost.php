<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Users\Controller\Frontend\Account;

use ACP3\Core;
use ACP3\Core\Authentication\Model\UserModelInterface;
use ACP3\Core\Modules\Helper\Action;
use ACP3\Modules\ACP3\Users;

class SettingsPost extends AbstractAction
{
    /**
     * @var \ACP3\Modules\ACP3\Users\Validation\AccountSettingsFormValidation
     */
    private $accountSettingsFormValidation;
    /**
     * @var Users\Model\UsersModel
     */
    private $usersModel;
    /**
     * @var Users\Model\AuthenticationModel
     */
    private $authenticationModel;
    /**
     * @var \ACP3\Core\Authentication\Model\UserModelInterface
     */
    private $user;
    /**
     * @var \ACP3\Core\Modules\Helper\Action
     */
    private $actionHelper;

    public function __construct(
        Core\Controller\Context\FrontendContext $context,
        Action $actionHelper,
        UserModelInterface $user,
        Users\Model\AuthenticationModel $authenticationModel,
        Users\Model\UsersModel $usersModel,
        Users\Validation\AccountSettingsFormValidation $accountSettingsFormValidation
    ) {
        parent::__construct($context, $user);

        $this->accountSettingsFormValidation = $accountSettingsFormValidation;
        $this->usersModel = $usersModel;
        $this->authenticationModel = $authenticationModel;
        $this->user = $user;
        $this->actionHelper = $actionHelper;
    }

    /**
     * @return array|string|\Symfony\Component\HttpFoundation\JsonResponse|\Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     *
     * @throws \Doctrine\DBAL\ConnectionException
     * @throws \Doctrine\DBAL\DBALException
     */
    public function __invoke()
    {
        return $this->actionHelper->handlePostAction(
            function () {
                $formData = $this->request->getPost()->all();

                $this->accountSettingsFormValidation->validate($formData);

                if (!empty($formData['new_pwd']) && !empty($formData['new_pwd_repeat'])) {
                    $formData['pwd'] = $formData['new_pwd'];
                }

                $bool = $this->usersModel->save($formData, $this->user->getUserId());

                $user = $this->usersModel->getOneById($this->user->getUserId());
                $cookie = $this->authenticationModel->setRememberMeCookie(
                    $this->user->getUserId(),
                    $user['remember_me_token']
                );

                $response = $this->actionHelper->setRedirectMessage(
                    $bool,
                    $this->translator->t('system', $bool !== false ? 'settings_success' : 'settings_error')
                );

                $response->headers->setCookie($cookie);

                return $response;
            }
        );
    }
}
