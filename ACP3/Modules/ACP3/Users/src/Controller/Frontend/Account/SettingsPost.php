<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Users\Controller\Frontend\Account;

use ACP3\Core;
use ACP3\Core\Authentication\Model\UserModelInterface;
use ACP3\Core\Helpers\FormAction;
use ACP3\Modules\ACP3\Users;

class SettingsPost extends AbstractAction
{
    /**
     * @var \ACP3\Core\Authentication\Model\UserModelInterface
     */
    private $user;

    public function __construct(
        Core\Controller\Context\WidgetContext $context,
        private FormAction $actionHelper,
        UserModelInterface $user,
        private Users\Model\AuthenticationModel $authenticationModel,
        private Users\Model\UsersModel $usersModel,
        private Users\Validation\AccountSettingsFormValidation $accountSettingsFormValidation
    ) {
        parent::__construct($context, $user);
        $this->user = $user;
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

                $this->accountSettingsFormValidation->validate($formData);

                if (!empty($formData['new_pwd']) && !empty($formData['new_pwd_repeat'])) {
                    $formData['pwd'] = $formData['new_pwd'];
                }

                $result = $this->usersModel->save($formData, $this->user->getUserId());

                $user = $this->usersModel->getOneById($this->user->getUserId());
                $cookie = $this->authenticationModel->setRememberMeCookie(
                    $this->user->getUserId(),
                    $user['remember_me_token']
                );

                $response = $this->actionHelper->setRedirectMessage(
                    $result,
                    $this->translator->t('system', $result ? 'settings_success' : 'settings_error')
                );

                $response->headers->setCookie($cookie);

                return $response;
            }
        );
    }
}
