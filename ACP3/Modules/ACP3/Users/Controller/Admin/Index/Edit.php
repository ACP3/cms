<?php
/**
 * Copyright (c) 2016 by the ACP3 Developers. See the LICENCE file at the top-level module directory for licencing details.
 */

namespace ACP3\Modules\ACP3\Users\Controller\Admin\Index;

use ACP3\Core;
use ACP3\Modules\ACP3\Permissions;
use ACP3\Modules\ACP3\Users;

/**
 * Class Edit
 * @package ACP3\Modules\ACP3\Users\Controller\Admin\Index
 */
class Edit extends AbstractFormAction
{
    /**
     * @var \ACP3\Core\Helpers\FormToken
     */
    protected $formTokenHelper;
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
     * Edit constructor.
     *
     * @param \ACP3\Core\Controller\Context\AdminContext $context
     * @param \ACP3\Core\Helpers\FormToken $formTokenHelper
     * @param \ACP3\Core\Helpers\Secure $secureHelper
     * @param \ACP3\Core\Helpers\Forms $formsHelpers
     * @param \ACP3\Modules\ACP3\Users\Model\AuthenticationModel $authenticationModel
     * @param Users\Model\UsersModel $usersModel
     * @param \ACP3\Modules\ACP3\Users\Validation\AdminFormValidation $adminFormValidation
     * @param \ACP3\Modules\ACP3\Permissions\Helpers $permissionsHelpers
     */
    public function __construct(
        Core\Controller\Context\AdminContext $context,
        Core\Helpers\FormToken $formTokenHelper,
        Core\Helpers\Secure $secureHelper,
        Core\Helpers\Forms $formsHelpers,
        Users\Model\AuthenticationModel $authenticationModel,
        Users\Model\UsersModel $usersModel,
        Users\Validation\AdminFormValidation $adminFormValidation,
        Permissions\Helpers $permissionsHelpers
    ) {
        parent::__construct($context, $formsHelpers);

        $this->formTokenHelper = $formTokenHelper;
        $this->secureHelper = $secureHelper;
        $this->authenticationModel = $authenticationModel;
        $this->adminFormValidation = $adminFormValidation;
        $this->permissionsHelpers = $permissionsHelpers;
        $this->usersModel = $usersModel;
    }

    /**
     * @param int $id
     *
     * @return array|\Symfony\Component\HttpFoundation\RedirectResponse
     * @throws \ACP3\Core\Controller\Exception\ResultNotExistsException
     */
    public function execute($id)
    {
        $user = $this->user->getUserInfo($id);

        if (!empty($user)) {
            $this->title->setPageTitlePostfix($user['nickname']);

            if ($this->request->getPost()->count() !== 0) {
                return $this->executePost($this->request->getPost()->all(), $id);
            }

            $userRoles = $this->acl->getUserRoleIds($id);
            $this->view->assign(
                $this->get('users.helpers.forms')->fetchUserSettingsFormFields(
                    $user['language'],
                    $user['time_zone'],
                    $user['address_display'],
                    $user['birthday_display'],
                    $user['country_display'],
                    $user['mail_display']
                )
            );
            $this->view->assign(
                $this->get('users.helpers.forms')->fetchUserProfileFormFields(
                    $user['birthday'],
                    $user['country'],
                    $user['gender']
                )
            );

            return [
                'roles' => $this->fetchUserRoles($userRoles),
                'super_user' => $this->fetchIsSuperUser($user['super_user']),
                'contact' => $this->get('users.helpers.forms')->fetchContactDetails(
                    $user['mail'],
                    $user['website'],
                    $user['icq'],
                    $user['skype']
                ),
                'form' => array_merge($user, $this->request->getPost()->all()),
                'form_token' => $this->formTokenHelper->renderFormToken()
            ];
        }

        throw new Core\Controller\Exception\ResultNotExistsException();
    }

    /**
     * @param array $formData
     * @param int $userId
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    protected function executePost(array $formData, $userId)
    {
        return $this->actionHelper->handleSaveAction(function () use ($formData, $userId) {
            $this->adminFormValidation
                ->setUserId($userId)
                ->validate($formData);

            $formData['time_zone'] = $formData['date_time_zone'];

            $this->permissionsHelpers->updateUserRoles($formData['roles'], $userId);

            if (!empty($formData['new_pwd']) && !empty($formData['new_pwd_repeat'])) {
                $salt = $this->secureHelper->salt(Users\Model\UserModel::SALT_LENGTH);
                $newPassword = $this->secureHelper->generateSaltedPassword($salt, $formData['new_pwd'], 'sha512');
                $formData['pwd'] = $newPassword;
                $formData['pwd_salt'] = $salt;
            }

            $bool = $this->usersModel->save($formData, $userId);

            $this->updateCurrentlyLoggedInUserCookie($userId);

            return $bool;
        });
    }

    /**
     * @param int $userId
     */
    protected function updateCurrentlyLoggedInUserCookie($userId)
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
