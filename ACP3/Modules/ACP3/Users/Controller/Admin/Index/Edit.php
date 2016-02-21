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
     * @var \ACP3\Modules\ACP3\Users\Model\UserRepository
     */
    protected $userRepository;
    /**
     * @var \ACP3\Modules\ACP3\Users\Validation\AdminFormValidation
     */
    protected $adminFormValidation;
    /**
     * @var \ACP3\Modules\ACP3\Permissions\Helpers
     */
    protected $permissionsHelpers;

    /**
     * Edit constructor.
     *
     * @param \ACP3\Core\Modules\Controller\AdminContext              $context
     * @param \ACP3\Core\Helpers\FormToken                            $formTokenHelper
     * @param \ACP3\Core\Helpers\Secure                               $secureHelper
     * @param \ACP3\Core\Helpers\Forms                                $formsHelpers
     * @param \ACP3\Modules\ACP3\Users\Model\UserRepository           $userRepository
     * @param \ACP3\Modules\ACP3\Users\Validation\AdminFormValidation $adminFormValidation
     * @param \ACP3\Modules\ACP3\Permissions\Helpers                  $permissionsHelpers
     */
    public function __construct(
        Core\Modules\Controller\AdminContext $context,
        Core\Helpers\FormToken $formTokenHelper,
        Core\Helpers\Secure $secureHelper,
        Core\Helpers\Forms $formsHelpers,
        Users\Model\UserRepository $userRepository,
        Users\Validation\AdminFormValidation $adminFormValidation,
        Permissions\Helpers $permissionsHelpers)
    {
        parent::__construct($context, $formsHelpers);

        $this->formTokenHelper = $formTokenHelper;
        $this->secureHelper = $secureHelper;
        $this->userRepository = $userRepository;
        $this->adminFormValidation = $adminFormValidation;
        $this->permissionsHelpers = $permissionsHelpers;
    }

    /**
     * @param int $id
     *
     * @return array|\Symfony\Component\HttpFoundation\RedirectResponse
     * @throws \ACP3\Core\Exceptions\ResultNotExists
     */
    public function execute($id)
    {
        if ($this->userRepository->resultExists($id) === true) {
            $user = $this->user->getUserInfo($id);

            $this->breadcrumb->setTitlePostfix($user['nickname']);

            if ($this->request->getPost()->isEmpty() === false) {
                return $this->executePost($this->request->getPost()->all(), $id);
            }

            $userRoles = $this->acl->getUserRoleIds($id);
            $this->view->assign(
                $this->get('users.helpers.forms')->fetchUserSettingsFormFields(
                    (int)$user['entries'],
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

        throw new Core\Exceptions\ResultNotExists();
    }

    /**
     * @param array $formData
     * @param int   $id
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    protected function executePost(array $formData, $id)
    {
        return $this->actionHelper->handleEditPostAction(function () use ($formData, $id) {
            $this->adminFormValidation
                ->setUserId($id)
                ->validate($formData);

            $updateValues = [
                'super_user' => (int)$formData['super_user'],
                'nickname' => Core\Functions::strEncode($formData['nickname']),
                'realname' => Core\Functions::strEncode($formData['realname']),
                'gender' => (int)$formData['gender'],
                'birthday' => $formData['birthday'],
                'birthday_display' => (int)$formData['birthday_display'],
                'mail' => $formData['mail'],
                'mail_display' => (int)$formData['mail_display'],
                'website' => Core\Functions::strEncode($formData['website']),
                'icq' => $formData['icq'],
                'skype' => Core\Functions::strEncode($formData['skype']),
                'street' => Core\Functions::strEncode($formData['street']),
                'house_number' => Core\Functions::strEncode($formData['house_number']),
                'zip' => Core\Functions::strEncode($formData['zip']),
                'city' => Core\Functions::strEncode($formData['city']),
                'address_display' => (int)$formData['address_display'],
                'country' => Core\Functions::strEncode($formData['country']),
                'country_display' => (int)$formData['country_display'],
                'date_format_long' => Core\Functions::strEncode($formData['date_format_long']),
                'date_format_short' => Core\Functions::strEncode($formData['date_format_short']),
                'time_zone' => $formData['date_time_zone'],
                'language' => $formData['language'],
                'entries' => (int)$formData['entries'],
            ];

            $this->permissionsHelpers->updateUserRoles($formData['roles'], $id);

            // Neues Passwort
            if (!empty($formData['new_pwd']) && !empty($formData['new_pwd_repeat'])) {
                $salt = $this->secureHelper->salt(Core\User::SALT_LENGTH);
                $newPassword = $this->secureHelper->generateSaltedPassword($salt, $formData['new_pwd'], 'sha512');
                $updateValues['pwd'] = $newPassword;
                $updateValues['pwd_salt'] = $salt;
            }

            $bool = $this->userRepository->update($updateValues, $id);

            // Falls sich der User selbst bearbeitet hat, Cookie aktualisieren
            if ($id == $this->user->getUserId() && $this->request->getCookies()->has(Core\User::AUTH_NAME)) {
                $user = $this->userRepository->getOneById($id);
                $this->user->setRememberMeCookie(
                    $id,
                    $user['remember_me_token'],
                    Core\User::REMEMBER_ME_COOKIE_LIFETIME
                );
            }

            $this->formTokenHelper->unsetFormToken();

            return $bool;
        });
    }
}
