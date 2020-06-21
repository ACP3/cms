<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Users\Controller\Admin\Index;

use ACP3\Core;
use ACP3\Core\Authentication\Model\UserModelInterface;
use ACP3\Core\Modules\Helper\Action;
use ACP3\Modules\ACP3\Permissions;
use ACP3\Modules\ACP3\Users;
use ACP3\Modules\ACP3\Users\Model\AuthenticationModel;

class EditPost extends Core\Controller\AbstractFrontendAction implements Core\Controller\InvokableActionInterface
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
     * @var \ACP3\Modules\ACP3\Users\Model\AuthenticationModel
     */
    private $authenticationModel;
    /**
     * @var Users\Model\UsersModel
     */
    private $usersModel;
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
        AuthenticationModel $authenticationModel,
        Users\Model\UsersModel $usersModel,
        Users\Validation\AdminFormValidation $adminFormValidation,
        Permissions\Helpers $permissionsHelpers
    ) {
        parent::__construct($context);

        $this->authenticationModel = $authenticationModel;
        $this->adminFormValidation = $adminFormValidation;
        $this->permissionsHelpers = $permissionsHelpers;
        $this->usersModel = $usersModel;
        $this->user = $user;
        $this->actionHelper = $actionHelper;
    }

    /**
     * @return array|string|\Symfony\Component\HttpFoundation\JsonResponse|\Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     *
     * @throws \Doctrine\DBAL\ConnectionException
     * @throws \Doctrine\DBAL\DBALException
     */
    public function __invoke(int $id)
    {
        return $this->actionHelper->handleSaveAction(function () use ($id) {
            $formData = $this->request->getPost()->all();

            $this->adminFormValidation
                ->setUserId($id)
                ->validate($formData);

            $this->permissionsHelpers->updateUserRoles($formData['roles'], $id);

            if (!empty($formData['new_pwd']) && !empty($formData['new_pwd_repeat'])) {
                $formData['pwd'] = $formData['new_pwd'];
            }

            $bool = $this->usersModel->save($formData, $id);

            $this->updateCurrentlyLoggedInUserCookie($id);

            return $bool;
        });
    }

    /**
     * @throws \Exception
     */
    protected function updateCurrentlyLoggedInUserCookie(int $userId): void
    {
        if ($userId === $this->user->getUserId() && $this->request->getCookies()->has(AuthenticationModel::AUTH_NAME)) {
            $user = $this->usersModel->getOneById($userId);
            $cookie = $this->authenticationModel->setRememberMeCookie(
                $userId,
                $user['remember_me_token']
            );
            $this->response->headers->setCookie($cookie);
        }
    }
}
