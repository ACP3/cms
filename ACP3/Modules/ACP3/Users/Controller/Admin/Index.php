<?php

namespace ACP3\Modules\ACP3\Users\Controller\Admin;

use ACP3\Core;
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
     * @var \ACP3\Modules\ACP3\Users\Model
     */
    protected $usersModel;
    /**
     * @var \ACP3\Modules\ACP3\Users\Validator
     */
    protected $usersValidator;
    /**
     * @var \ACP3\Modules\ACP3\Permissions\Helpers
     */
    protected $permissionsHelpers;

    /**
     * @param \ACP3\Core\Modules\Controller\AdminContext $context
     * @param \ACP3\Core\Date                            $date
     * @param \ACP3\Core\Helpers\FormToken               $formTokenHelper
     * @param \ACP3\Core\Helpers\Secure                  $secureHelper
     * @param \ACP3\Core\Helpers\Forms                   $formsHelpers
     * @param \ACP3\Modules\ACP3\Users\Model             $usersModel
     * @param \ACP3\Modules\ACP3\Users\Validator         $usersValidator
     * @param \ACP3\Modules\ACP3\Permissions\Helpers     $permissionsHelpers
     */
    public function __construct(
        Core\Modules\Controller\AdminContext $context,
        Core\Date $date,
        Core\Helpers\FormToken $formTokenHelper,
        Core\Helpers\Secure $secureHelper,
        Core\Helpers\Forms $formsHelpers,
        Users\Model $usersModel,
        Users\Validator $usersValidator,
        Permissions\Helpers $permissionsHelpers)
    {
        parent::__construct($context);

        $this->date = $date;
        $this->formTokenHelper = $formTokenHelper;
        $this->secureHelper = $secureHelper;
        $this->formsHelpers = $formsHelpers;
        $this->usersModel = $usersModel;
        $this->usersValidator = $usersValidator;
        $this->permissionsHelpers = $permissionsHelpers;
    }

    public function actionCreate()
    {
        if ($this->request->getPost()->isEmpty() === false) {
            $this->_createPost($this->request->getPost()->getAll());
        }

        $systemSettings = $this->config->getSettings('system');

        // Zugriffslevel holen
        $roles = $this->acl->getAllRoles();
        $c_roles = count($roles);
        for ($i = 0; $i < $c_roles; ++$i) {
            $roles[$i]['name'] = str_repeat('&nbsp;&nbsp;', $roles[$i]['level']) . $roles[$i]['name'];
            $roles[$i]['selected'] = $this->formsHelpers->selectEntry('roles', $roles[$i]['id']);
        }
        $this->view->assign('roles', $roles);

        // Super User
        $lang_superUser = [$this->lang->t('system', 'yes'), $this->lang->t('system', 'no')];
        $this->view->assign('super_user', $this->formsHelpers->selectGenerator('super_user', [1, 0], $lang_superUser, 0, 'checked'));

        // Sprache
        $this->view->assign('languages', $this->lang->getLanguagePack($this->request->getPost()->get('language', $systemSettings['lang'])));

        // Einträge pro Seite
        $this->view->assign('entries', $this->formsHelpers->recordsPerPage($systemSettings['entries']));

        // Zeitzonen
        $this->view->assign('time_zones', $this->get('core.helpers.date')->getTimeZones($systemSettings['date_time_zone']));

        // Geschlecht
        $lang_gender = [
            $this->lang->t('users', 'gender_not_specified'),
            $this->lang->t('users', 'gender_female'),
            $this->lang->t('users', 'gender_male')
        ];
        $this->view->assign('gender', $this->formsHelpers->selectGenerator('gender', [1, 2, 3], $lang_gender, ''));

        // Geburtstag
        $datepickerParams = [
            'constrainInput' => 'true',
            'changeMonth' => 'true',
            'changeYear' => 'true',
            'yearRange' => '\'-50:+0\''
        ];
        $this->view->assign('birthday_datepicker', $this->get('core.helpers.date')->datepicker('birthday', '', 'Y-m-d', $datepickerParams, 0, false, true));

        // Kontaktangaben
        $this->view->assign('contact', $this->fetchContactDetails());

        $countries = Core\Lang::worldCountries();
        $countries_select = [];
        foreach ($countries as $key => $value) {
            $countries_select[] = [
                'value' => $key,
                'lang' => $value,
                'selected' => $this->formsHelpers->selectEntry('countries', $key),
            ];
        }
        $this->view->assign('countries', $countries_select);

        $lang_mail_display = [$this->lang->t('system', 'yes'), $this->lang->t('system', 'no')];
        $this->view->assign('mail_display', $this->formsHelpers->selectGenerator('mail_display', [1, 0], $lang_mail_display, 0, 'checked'));

        $lang_address_display = [$this->lang->t('system', 'yes'), $this->lang->t('system', 'no')];
        $this->view->assign('address_display', $this->formsHelpers->selectGenerator('address_display', [1, 0], $lang_address_display, 0, 'checked'));

        $lang_country_display = [$this->lang->t('system', 'yes'), $this->lang->t('system', 'no')];
        $this->view->assign('country_display', $this->formsHelpers->selectGenerator('country_display', [1, 0], $lang_country_display, 0, 'checked'));

        $lang_birthday_display = [
            $this->lang->t('users', 'birthday_hide'),
            $this->lang->t('users', 'birthday_display_completely'),
            $this->lang->t('users', 'birthday_hide_year')
        ];
        $this->view->assign('birthday_display', $this->formsHelpers->selectGenerator('birthday_display', [0, 1, 2], $lang_birthday_display, 0, 'checked'));

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

        $this->view->assign('form', array_merge($defaults, $this->request->getPost()->getAll()));

        $this->formTokenHelper->generateFormToken($this->request->getQuery());
    }

    /**
     * @param string $action
     *
     * @throws \ACP3\Core\Exceptions\ResultNotExists
     */
    public function actionDelete($action = '')
    {
        $this->handleCustomDeleteAction(
            $action,
            function($items) {
                $bool = $isAdminUser = $selfDelete = false;
                foreach ($items as $item) {
                    if ($item == 1) {
                        $isAdminUser = true;
                    } else {
                        // Falls sich der User selbst gelöscht hat, diesen auch gleich abmelden
                        if ($item == $this->auth->getUserId()) {
                            $this->auth->logout();
                            $selfDelete = true;
                        }
                        $bool = $this->usersModel->delete($item);
                    }
                }
                if ($isAdminUser === true) {
                    $bool = false;
                    $text = $this->lang->t('users', 'admin_user_undeletable');
                } else {
                    $text = $this->lang->t('system', $bool !== false ? 'delete_success' : 'delete_error');
                }

                $this->redirectMessages()->setMessage($bool, $text, $selfDelete === true ? ROOT_DIR : '');
            }
        );
    }

    /**
     * @param int $id
     *
     * @throws \ACP3\Core\Exceptions\ResultNotExists
     */
    public function actionEdit($id)
    {
        if ($this->usersModel->resultExists($id) === true) {
            $user = $this->auth->getUserInfo($id);

            $this->breadcrumb->setTitlePostfix($user['nickname']);

            if ($this->request->getPost()->isEmpty() === false) {
                $this->_editPost($this->request->getPost()->getAll(), $id);
            }

            // Zugriffslevel holen
            $roles = $this->acl->getAllRoles();
            $c_roles = count($roles);
            $userRoles = $this->acl->getUserRoleIds($id);
            for ($i = 0; $i < $c_roles; ++$i) {
                $roles[$i]['name'] = str_repeat('&nbsp;&nbsp;', $roles[$i]['level']) . $roles[$i]['name'];
                $roles[$i]['selected'] = $this->formsHelpers->selectEntry('roles', $roles[$i]['id'], in_array($roles[$i]['id'], $userRoles) ? $roles[$i]['id'] : '');
            }
            $this->view->assign('roles', $roles);

            // Super User
            $langSuperUser = [$this->lang->t('system', 'yes'), $this->lang->t('system', 'no')];
            $this->view->assign('super_user', $this->formsHelpers->selectGenerator('super_user', [1, 0], $langSuperUser, $user['super_user'], 'checked'));

            // Sprache
            $this->view->assign('languages', $this->lang->getLanguagePack($this->request->getPost()->get('language', $user['language'])));

            // Einträge pro Seite
            $this->view->assign('entries', $this->formsHelpers->recordsPerPage((int)$user['entries']));

            // Zeitzonen
            $this->view->assign('time_zones', $this->get('core.helpers.date')->getTimeZones($user['time_zone']));

            // Geschlecht
            $lang_gender = [
                $this->lang->t('users', 'gender_not_specified'),
                $this->lang->t('users', 'gender_female'),
                $this->lang->t('users', 'gender_male')
            ];
            $this->view->assign('gender', $this->formsHelpers->selectGenerator('gender', [1, 2, 3], $lang_gender, $user['gender']));

            // Geburtstag
            $datepickerParams = ['constrainInput' => 'true', 'changeMonth' => 'true', 'changeYear' => 'true', 'yearRange' => '\'-50:+0\''];
            $this->view->assign('birthday_datepicker', $this->get('core.helpers.date')->datepicker('birthday', $user['birthday'], 'Y-m-d', $datepickerParams, 0, false, true));

            // Kontaktangaben
            $this->view->assign('contact', $this->fetchContactDetails(
                $user['mail'],
                $user['website'],
                $user['icq'],
                $user['skype']
            ));

            $countries = Core\Lang::worldCountries();
            $countries_select = [];
            foreach ($countries as $key => $value) {
                $countries_select[] = [
                    'value' => $key,
                    'lang' => $value,
                    'selected' => $this->formsHelpers->selectEntry('countries', $key, $user['country']),
                ];
            }
            $this->view->assign('countries', $countries_select);

            $lang_mailDisplay = [$this->lang->t('system', 'yes'), $this->lang->t('system', 'no')];
            $this->view->assign('mail_display', $this->formsHelpers->selectGenerator('mail_display', [1, 0], $lang_mailDisplay, $user['mail_display'], 'checked'));

            $lang_addressDisplay = [$this->lang->t('system', 'yes'), $this->lang->t('system', 'no')];
            $this->view->assign('address_display', $this->formsHelpers->selectGenerator('address_display', [1, 0], $lang_addressDisplay, $user['address_display'], 'checked'));

            $lang_countryDisplay = [$this->lang->t('system', 'yes'), $this->lang->t('system', 'no')];
            $this->view->assign('country_display', $this->formsHelpers->selectGenerator('country_display', [1, 0], $lang_countryDisplay, $user['country_display'], 'checked'));

            $lang_birthdayDisplay = [
                $this->lang->t('users', 'birthday_hide'),
                $this->lang->t('users', 'birthday_display_completely'),
                $this->lang->t('users', 'birthday_hide_year')
            ];
            $this->view->assign('birthday_display', $this->formsHelpers->selectGenerator('birthday_display', [0, 1, 2], $lang_birthdayDisplay, $user['birthday_display'], 'checked'));

            $this->view->assign('form', array_merge($user, $this->request->getPost()->getAll()));

            $this->formTokenHelper->generateFormToken($this->request->getQuery());
        } else {
            throw new Core\Exceptions\ResultNotExists();
        }
    }

    public function actionSettings()
    {
        if ($this->request->getPost()->isEmpty() === false) {
            $this->_settingsPost($this->request->getPost()->getAll());
        }

        $settings = $this->config->getSettings('users');

        $lang_languages = [$this->lang->t('system', 'yes'), $this->lang->t('system', 'no')];
        $this->view->assign('languages', $this->formsHelpers->selectGenerator('language_override', [1, 0], $lang_languages, $settings['language_override'], 'checked'));

        $lang_entries = [$this->lang->t('system', 'yes'), $this->lang->t('system', 'no')];
        $this->view->assign('entries', $this->formsHelpers->selectGenerator('entries_override', [1, 0], $lang_entries, $settings['entries_override'], 'checked'));

        $lang_registration = [$this->lang->t('system', 'yes'), $this->lang->t('system', 'no')];
        $this->view->assign('registration', $this->formsHelpers->selectGenerator('enable_registration', [1, 0], $lang_registration, $settings['enable_registration'], 'checked'));

        $this->view->assign('form', array_merge(['mail' => $settings['mail']], $this->request->getPost()->getAll()));

        $this->formTokenHelper->generateFormToken($this->request->getQuery());
    }

    public function actionIndex()
    {
        $users = $this->usersModel->getAllInAcp();
        $c_users = count($users);

        if ($c_users > 0) {
            $canDelete = $this->acl->hasPermission('admin/users/index/delete');
            $config = [
                'element' => '#acp-table',
                'sort_col' => $canDelete === true ? 1 : 0,
                'sort_dir' => 'asc',
                'hide_col_sort' => $canDelete === true ? 0 : '',
                'records_per_page' => $this->auth->entries
            ];
            $this->view->assign('datatable_config', $config);

            for ($i = 0; $i < $c_users; ++$i) {
                $users[$i]['roles'] = implode(', ', $this->acl->getUserRoleNames($users[$i]['id']));
            }
            $this->view->assign('users', $users);
            $this->view->assign('can_delete', $canDelete);
        }
    }

    /**
     * @param $formData
     */
    protected function _createPost($formData)
    {
        $this->handleCreatePostAction(function() use ($formData) {
            $this->usersValidator->validate($formData);

            $salt = $this->secureHelper->salt(12);

            $insertValues = [
                'id' => '',
                'super_user' => (int)$formData['super_user'],
                'nickname' => Core\Functions::strEncode($formData['nickname']),
                'pwd' => $this->secureHelper->generateSaltedPassword($salt, $formData['pwd']) . ':' . $salt,
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

            $lastId = $this->usersModel->insert($insertValues);

            $this->permissionsHelpers->updateUserRoles($formData['roles'], $lastId);

            $this->formTokenHelper->unsetFormToken($this->request->getQuery());

            return $lastId;
        });
    }

    /**
     * @param array $formData
     * @param int   $id
     */
    protected function _editPost(array $formData, $id)
    {
        $this->handleEditPostAction(function() use ($formData, $id) {
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
                $salt = $this->secureHelper->salt(12);
                $newPassword = $this->secureHelper->generateSaltedPassword($salt, $formData['new_pwd']);
                $updateValues['pwd'] = $newPassword . ':' . $salt;
            }

            $bool = $this->usersModel->update($updateValues, $id);

            // Falls sich der User selbst bearbeitet hat, Cookie aktualisieren
            if ($this->request->getParameters()->get('id') == $this->auth->getUserId()) {
                $cookieArray = explode('|', base64_decode($this->request->getCookie()->get('ACP3_AUTH', '')));
                $this->auth->setCookie($formData['nickname'], isset($newPassword) ? $newPassword : $cookieArray[1], 3600);
            }

            $this->formTokenHelper->unsetFormToken($this->request->getQuery());

            return $bool;
        });
    }

    /**
     * @param array $formData
     */
    protected function _settingsPost(array $formData)
    {
        $this->handleSettingsPostAction(function () use ($formData) {
            $this->usersValidator->validateSettings($formData);

            $data = [
                'enable_registration' => $formData['enable_registration'],
                'entries_override' => $formData['entries_override'],
                'language_override' => $formData['language_override'],
                'mail' => $formData['mail']
            ];

            $this->formTokenHelper->unsetFormToken($this->request->getQuery());

            return $this->config->setSettings($data, 'users');
        });
    }

    /**
     * @param string $defaultMail
     * @param string $defaultWebsite
     * @param string $defaultIcqNumber
     * @param string $defaultSkypeName
     *
     * @return array
     */
    private function fetchContactDetails(
        $defaultMail = '',
        $defaultWebsite = '',
        $defaultIcqNumber = '',
        $defaultSkypeName = ''
    )
    {
        return [
            [
                'name' => 'mail',
                'lang' => $this->lang->t('system', 'email_address'),
                'value' => $this->request->getPost()->get('mail', $defaultMail),
                'maxlength' => '120',
            ],
            [
                'name' => 'website',
                'lang' => $this->lang->t('system', 'website'),
                'value' => $this->request->getPost()->get('website', $defaultWebsite),
                'maxlength' => '120',
            ],
            [
                'name' => 'icq',
                'lang' => $this->lang->t('users', 'icq'),
                'value' => $this->request->getPost()->get('icq', $defaultIcqNumber),
                'maxlength' => '9',
            ],
            [
                'name' => 'skype',
                'lang' => $this->lang->t('users', 'skype'),
                'value' => $this->request->getPost()->get('skype', $defaultSkypeName),
                'maxlength' => '28',
            ]
        ];
    }
}
