<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Users\Controller\Admin\Index;

use ACP3\Core;
use ACP3\Modules\ACP3\Permissions;
use ACP3\Modules\ACP3\Users;

class Manage extends Core\Controller\AbstractFrontendAction
{
    /**
     * @var \ACP3\Core\Helpers\Secure
     */
    protected $secureHelper;
    /**
     * @var \ACP3\Modules\ACP3\Users\Validation\AdminFormValidation
     */
    protected $adminFormValidation;
    /**
     * @var \ACP3\Modules\ACP3\Permissions\Helpers
     */
    protected $permissionsHelpers;
    /**
     * @var \ACP3\Modules\ACP3\Users\Model\AuthenticationModel
     */
    protected $authenticationModel;
    /**
     * @var Users\Model\UsersModel
     */
    protected $usersModel;
    /**
     * @var Core\View\Block\RepositoryAwareFormBlockInterface
     */
    private $block;

    /**
     * Manage constructor.
     *
     * @param \ACP3\Core\Controller\Context\FrontendContext $context
     * @param Core\View\Block\RepositoryAwareFormBlockInterface $block
     * @param \ACP3\Core\Helpers\Secure $secureHelper
     * @param \ACP3\Modules\ACP3\Users\Model\AuthenticationModel $authenticationModel
     * @param Users\Model\UsersModel $usersModel
     * @param \ACP3\Modules\ACP3\Users\Validation\AdminFormValidation $adminFormValidation
     * @param \ACP3\Modules\ACP3\Permissions\Helpers $permissionsHelpers
     */
    public function __construct(
        Core\Controller\Context\FrontendContext $context,
        Core\View\Block\RepositoryAwareFormBlockInterface $block,
        Core\Helpers\Secure $secureHelper,
        Users\Model\AuthenticationModel $authenticationModel,
        Users\Model\UsersModel $usersModel,
        Users\Validation\AdminFormValidation $adminFormValidation,
        Permissions\Helpers $permissionsHelpers
    ) {
        parent::__construct($context);

        $this->secureHelper = $secureHelper;
        $this->authenticationModel = $authenticationModel;
        $this->adminFormValidation = $adminFormValidation;
        $this->permissionsHelpers = $permissionsHelpers;
        $this->usersModel = $usersModel;
        $this->block = $block;
    }

    /**
     * @param int|null $id
     *
     * @return array
     */
    public function execute(?int $id)
    {
        return $this->block
            ->setDataById($id)
            ->setRequestData($this->request->getPost()->all())
            ->render();
    }

    /**
     * @param int|null $id
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function executePost(?int $id)
    {
        return $this->actionHelper->handleSaveAction(function () use ($id) {
            $formData = $this->request->getPost()->all();

            if ($id !== null) {
                $this->adminFormValidation->setUserId($id);
            }

            $this->adminFormValidation->validate($formData);

            if ($id === null) {
                $salt = $this->generatePasswordSalt();
                $formData = \array_merge($formData, [
                    'pwd' => $this->secureHelper->generateSaltedPassword($salt, $formData['pwd'], 'sha512'),
                    'pwd_salt' => $salt,
                    'registration_date' => 'now',
                ]);
            } elseif (!empty($formData['new_pwd']) && !empty($formData['new_pwd_repeat'])) {
                $salt = $this->generatePasswordSalt();
                $newPassword = $this->secureHelper->generateSaltedPassword($salt, $formData['new_pwd'], 'sha512');
                $formData['pwd'] = $newPassword;
                $formData['pwd_salt'] = $salt;
            }

            $result = $this->usersModel->save($formData, $id);

            if ($result !== false) {
                $this->permissionsHelpers->updateUserRoles($formData['roles'], $result);
                $this->updateCurrentlyLoggedInUserCookie($result);
            }

            return $result;
        });
    }

    private function generatePasswordSalt(): string
    {
        return $this->secureHelper->salt(Users\Model\UserModel::SALT_LENGTH);
    }

    /**
     * @param int $userId
     */
    protected function updateCurrentlyLoggedInUserCookie(int $userId)
    {
        if ($userId == $this->user->getUserId() && $this->request->getCookies()->has(Users\Model\AuthenticationModel::AUTH_NAME)) {
            $user = $this->usersModel->getOneById($userId);
            $cookie = $this->authenticationModel->setRememberMeCookie(
                $userId,
                $user['remember_me_token']
            );
            $this->response->headers->setCookie($cookie);
        }
    }
}
