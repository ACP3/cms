<?php

namespace ACP3\Modules\ACP3\Users\Controller\Admin;

use ACP3\Core;
use ACP3\Core\Helpers\Country;
use ACP3\Modules\ACP3\Permissions;
use ACP3\Modules\ACP3\Users;

/**
 * Class Index
 * @package ACP3\Modules\ACP3\Users\Controller\Admin
 */
class Index extends Core\Modules\AdminController
{
    /**
     * @var \ACP3\Core\Date
     */
    protected $date;
    /**
     * @var \ACP3\Core\Helpers\Secure
     */
    protected $secureHelper;
    /**
     * @var \ACP3\Core\Helpers\FormToken
     */
    protected $formTokenHelper;
    /**
     * @var \ACP3\Core\Helpers\Forms
     */
    protected $formsHelpers;
    /**
     * @var \ACP3\Modules\ACP3\Users\Model\UserRepository
     */
    protected $userRepository;
    /**
     * @var \ACP3\Modules\ACP3\Users\Validator\Admin
     */
    protected $usersValidator;
    /**
     * @var \ACP3\Modules\ACP3\Permissions\Helpers
     */
    protected $permissionsHelpers;

    /**
     * @param \ACP3\Core\Modules\Controller\AdminContext    $context
     * @param \ACP3\Core\Date                               $date
     * @param \ACP3\Core\Helpers\FormToken                  $formTokenHelper
     * @param \ACP3\Core\Helpers\Secure                     $secureHelper
     * @param \ACP3\Core\Helpers\Forms                      $formsHelpers
     * @param \ACP3\Modules\ACP3\Users\Model\UserRepository $userRepository
     * @param \ACP3\Modules\ACP3\Users\Validator\Admin      $usersValidator
     * @param \ACP3\Modules\ACP3\Permissions\Helpers        $permissionsHelpers
     */
    public function __construct(
        Core\Modules\Controller\AdminContext $context,
        Core\Date $date,
        Core\Helpers\FormToken $formTokenHelper,
        Core\Helpers\Secure $secureHelper,
        Core\Helpers\Forms $formsHelpers,
        Users\Model\UserRepository $userRepository,
        Users\Validator\Admin $usersValidator,
        Permissions\Helpers $permissionsHelpers)
    {
        parent::__construct($context);

        $this->date = $date;
        $this->formTokenHelper = $formTokenHelper;
        $this->secureHelper = $secureHelper;
        $this->formsHelpers = $formsHelpers;
        $this->userRepository = $userRepository;
        $this->usersValidator = $usersValidator;
        $this->permissionsHelpers = $permissionsHelpers;
    }

    /**
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function actionCreate()
    {
        if ($this->request->getPost()->isEmpty() === false) {
            return $this->_createPost($this->request->getPost()->all());
        }

        $systemSettings = $this->config->getSettings('system');

        $this->view->assign('roles', $this->fetchUserRoles());
        $this->view->assign('super_user', $this->fetchIsSuperUser());
        $this->view->assign('contact', $this->get('users.helpers.forms')->fetchContactDetails());
        $this->view->assign(
            $this->get('users.helpers.forms')->fetchUserSettingsFormFields(
                (int)$systemSettings['entries'],
                $systemSettings['lang'],
                $systemSettings['date_time_zone']
            )
        );
        $this->view->assign($this->get('users.helpers.forms')->fetchUserProfileFormFields());

        $defaults = [
            'nickname' => '',
            'realname' => '',
            'mail' => '',
            'website' => '',
            'street' => '',
            'house_number' => '',
            'zip' => '',
            'city' => '',
            'date_format_long' => $systemSettings['date_format_long'],
            'date_format_short' => $systemSettings['date_format_short']
        ];

        $this->view->assign('form', array_merge($defaults, $this->request->getPost()->all()));

        $this->formTokenHelper->generateFormToken();
    }

    /**
     * @param string $action
     *
     * @return mixed
     * @throws \ACP3\Core\Exceptions\ResultNotExists
     */
    public function actionDelete($action = '')
    {
        return $this->actionHelper->handleCustomDeleteAction(
            $this,
            $action,
            function ($items) {
                $bool = $isAdminUser = $selfDelete = false;
                foreach ($items as $item) {
                    if ($item == 1) {
                        $isAdminUser = true;
                    } else {
                        // Falls sich der User selbst gelöscht hat, diesen auch gleich abmelden
                        if ($item == $this->user->getUserId()) {
                            $this->user->logout();
                            $selfDelete = true;
                        }
                        $bool = $this->userRepository->delete($item);
                    }
                }
                if ($isAdminUser === true) {
                    $bool = false;
                    $text = $this->lang->t('users', 'admin_user_undeletable');
                } else {
                    $text = $this->lang->t('system', $bool !== false ? 'delete_success' : 'delete_error');
                }

                return $this->redirectMessages()->setMessage($bool, $text, $selfDelete === true ? ROOT_DIR : '');
            }
        );
    }

    /**
     * @param int $id
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     * @throws \ACP3\Core\Exceptions\ResultNotExists
     */
    public function actionEdit($id)
    {
        if ($this->userRepository->resultExists($id) === true) {
            $user = $this->user->getUserInfo($id);

            $this->breadcrumb->setTitlePostfix($user['nickname']);

            if ($this->request->getPost()->isEmpty() === false) {
                return $this->_editPost($this->request->getPost()->all(), $id);
            }

            $userRoles = $this->acl->getUserRoleIds($id);
            $this->view->assign('roles', $this->fetchUserRoles($userRoles));
            $this->view->assign('super_user', $this->fetchIsSuperUser($user['super_user']));
            $this->view->assign('contact', $this->get('users.helpers.forms')->fetchContactDetails(
                $user['mail'],
                $user['website'],
                $user['icq'],
                $user['skype']
            ));
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

            $this->view->assign('form', array_merge($user, $this->request->getPost()->all()));

            $this->formTokenHelper->generateFormToken();
        } else {
            throw new Core\Exceptions\ResultNotExists();
        }
    }

    /**
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function actionSettings()
    {
        if ($this->request->getPost()->isEmpty() === false) {
            return $this->_settingsPost($this->request->getPost()->all());
        }

        $settings = $this->config->getSettings('users');

        $this->view->assign('languages', $this->formsHelpers->yesNoCheckboxGenerator('language_override', $settings['language_override']));

        $this->view->assign('entries', $this->formsHelpers->yesNoCheckboxGenerator('entries_override', $settings['entries_override']));

        $this->view->assign('registration', $this->formsHelpers->yesNoCheckboxGenerator('enable_registration', $settings['enable_registration']));

        $this->view->assign('form', array_merge(['mail' => $settings['mail']], $this->request->getPost()->all()));

        $this->formTokenHelper->generateFormToken();
    }

    public function actionIndex()
    {
        $users = $this->userRepository->getAllInAcp();

        /** @var Core\Helpers\DataGrid $dataGrid */
        $dataGrid = $this->get('core.helpers.data_grid');
        $dataGrid
            ->setResults($users)
            ->setRecordsPerPage($this->user->getEntriesPerPage())
            ->setIdentifier('#acp-table')
            ->setResourcePathDelete('admin/users/index/delete')
            ->setResourcePathEdit('admin/users/index/edit');

        $dataGrid
            ->addColumn([
                'label' => $this->lang->t('users', 'nickname'),
                'type' => 'text',
                'fields' => ['nickname'],
                'default_sort' => true
            ], 40)
            ->addColumn([
                'label' => $this->lang->t('system', 'email_address'),
                'type' => 'text',
                'fields' => ['mail'],
            ], 30)
            ->addColumn([
                'label' => $this->lang->t('permissions', 'roles'),
                'type' => 'user_roles',
                'fields' => ['id'],
            ], 20)
            ->addColumn([
                'label' => $this->lang->t('system', 'id'),
                'type' => 'integer',
                'fields' => ['id'],
                'primary' => true
            ], 10);

        return [
            'grid' => $dataGrid->render(),
            'show_mass_delete_button' => count($users) > 0
        ];
    }

    /**
     * @param $formData
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    protected function _createPost($formData)
    {
        return $this->actionHelper->handleCreatePostAction(function () use ($formData) {
            $this->usersValidator->validate($formData);

            $salt = $this->secureHelper->salt(15);

            $insertValues = [
                'id' => '',
                'super_user' => (int)$formData['super_user'],
                'nickname' => Core\Functions::strEncode($formData['nickname']),
                'pwd' => $this->secureHelper->generateSaltedPassword($salt, $formData['pwd'], 'sha512'),
                'pwd_salt' => $salt,
                'realname' => Core\Functions::strEncode($formData['realname']),
                'gender' => (int)$formData['gender'],
                'birthday' => $formData['birthday'],
                'birthday_display' => (int)$formData['birthday_display'],
                'mail' => $formData['mail'],
                'mail_display' => isset($formData['mail_display']) ? 1 : 0,
                'website' => Core\Functions::strEncode($formData['website']),
                'icq' => $formData['icq'],
                'skype' => Core\Functions::strEncode($formData['skype']),
                'street' => Core\Functions::strEncode($formData['street']),
                'house_number' => Core\Functions::strEncode($formData['house_number']),
                'zip' => Core\Functions::strEncode($formData['zip']),
                'city' => Core\Functions::strEncode($formData['city']),
                'address_display' => isset($formData['address_display']) ? 1 : 0,
                'country' => Core\Functions::strEncode($formData['country']),
                'country_display' => isset($formData['country_display']) ? 1 : 0,
                'date_format_long' => Core\Functions::strEncode($formData['date_format_long']),
                'date_format_short' => Core\Functions::strEncode($formData['date_format_short']),
                'time_zone' => $formData['date_time_zone'],
                'language' => $formData['language'],
                'entries' => (int)$formData['entries'],
                'draft' => '',
                'registration_date' => $this->date->getCurrentDateTime(),
            ];

            $lastId = $this->userRepository->insert($insertValues);

            $this->permissionsHelpers->updateUserRoles($formData['roles'], $lastId);

            $this->formTokenHelper->unsetFormToken();

            return $lastId;
        });
    }

    /**
     * @param array $formData
     * @param int   $id
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    protected function _editPost(array $formData, $id)
    {
        return $this->actionHelper->handleEditPostAction(function () use ($formData, $id) {
            $this->usersValidator->validate($formData, $id);

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

    /**
     * @param array $formData
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    protected function _settingsPost(array $formData)
    {
        return $this->actionHelper->handleSettingsPostAction(function () use ($formData) {
            $this->usersValidator->validateSettings($formData);

            $data = [
                'enable_registration' => $formData['enable_registration'],
                'entries_override' => $formData['entries_override'],
                'language_override' => $formData['language_override'],
                'mail' => $formData['mail']
            ];

            $this->formTokenHelper->unsetFormToken();

            return $this->config->setSettings($data, 'users');
        });
    }

    /**
     * @param array $userRoles
     *
     * @return array
     */
    protected function fetchUserRoles(array $userRoles = [])
    {
        $roles = $this->acl->getAllRoles();
        $c_roles = count($roles);
        for ($i = 0; $i < $c_roles; ++$i) {
            $roles[$i]['name'] = str_repeat('&nbsp;&nbsp;', $roles[$i]['level']) . $roles[$i]['name'];
            $roles[$i]['selected'] = $this->formsHelpers->selectEntry('roles', $roles[$i]['id'], in_array($roles[$i]['id'], $userRoles) ? $roles[$i]['id'] : '');
        }
        return $roles;
    }

    /**
     * @param int $value
     *
     * @return array
     */
    protected function fetchIsSuperUser($value = 0)
    {
        return $this->formsHelpers->yesNoCheckboxGenerator('super_user', $value);
    }
}
