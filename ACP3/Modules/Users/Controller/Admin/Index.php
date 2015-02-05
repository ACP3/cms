<?php

namespace ACP3\Modules\Users\Controller\Admin;

use ACP3\Core;
use ACP3\Modules\Permissions;
use ACP3\Modules\Users;

/**
 * Class Index
 * @package ACP3\Modules\Users\Controller\Admin
 */
class Index extends Core\Modules\Controller\Admin
{
    /**
     * @var \ACP3\Core\Date
     */
    protected $date;
    /**
     * @var \ACP3\Core\Helpers\Secure
     */
    protected $secureHelpers;
    /**
     * @var \ACP3\Core\Helpers\Forms
     */
    protected $formsHelpers;
    /**
     * @var \ACP3\Modules\Users\Model
     */
    protected $usersModel;
    /**
     * @var \ACP3\Modules\Users\Validator
     */
    protected $usersValidator;
    /**
     * @var \ACP3\Modules\Permissions\Model
     */
    protected $permissionsModel;

    /**
     * @param \ACP3\Core\Context\Admin        $context
     * @param \ACP3\Core\Date                 $date
     * @param \ACP3\Core\Helpers\Secure       $secureHelper
     * @param \ACP3\Core\Helpers\Forms        $formsHelpers
     * @param \ACP3\Modules\Users\Model       $usersModel
     * @param \ACP3\Modules\Users\Validator   $usersValidator
     * @param \ACP3\Core\Config               $usersConfig
     * @param \ACP3\Modules\Permissions\Model $permissionsModel
     */
    public function __construct(
        Core\Context\Admin $context,
        Core\Date $date,
        Core\Helpers\Secure $secureHelper,
        Core\Helpers\Forms $formsHelpers,
        Users\Model $usersModel,
        Users\Validator $usersValidator,
        Core\Config $usersConfig,
        Permissions\Model $permissionsModel)
    {
        parent::__construct($context);

        $this->date = $date;
        $this->secureHelpers = $secureHelper;
        $this->formsHelpers = $formsHelpers;
        $this->usersModel = $usersModel;
        $this->usersValidator = $usersValidator;
        $this->usersConfig = $usersConfig;
        $this->permissionsModel = $permissionsModel;
    }

    public function actionCreate()
    {
        if (empty($_POST) === false) {
            $this->_createPost($_POST);
        }

        $systemSettings = $this->systemConfig->getSettings();

        // Zugriffslevel holen
        $roles = $this->acl->getAllRoles();
        $c_roles = count($roles);
        for ($i = 0; $i < $c_roles; ++$i) {
            $roles[$i]['name'] = str_repeat('&nbsp;&nbsp;', $roles[$i]['level']) . $roles[$i]['name'];
            $roles[$i]['selected'] = $this->formsHelpers->selectEntry('roles', $roles[$i]['id']);
        }
        $this->view->assign('roles', $roles);

        // Super User
        $lang_super_user = [$this->lang->t('system', 'yes'), $this->lang->t('system', 'no')];
        $this->view->assign('super_user', $this->formsHelpers->selectGenerator('super_user', [1, 0], $lang_super_user, 0, 'checked'));

        // Sprache
        $this->view->assign('languages', $this->lang->getLanguages(isset($_POST['language']) ? $_POST['language'] : $systemSettings['lang']));

        // Einträge pro Seite
        $this->view->assign('entries', $this->formsHelpers->recordsPerPage($systemSettings['entries']));

        // Zeitzonen
        $this->view->assign('time_zones', $this->date->getTimeZones($systemSettings['date_time_zone']));

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
        $this->view->assign('birthday_datepicker', $this->date->datepicker('birthday', '', 'Y-m-d', $datepickerParams, 0, false, true));

        // Kontaktangaben
        $contact = [];
        $contact[0]['name'] = 'mail';
        $contact[0]['lang'] = $this->lang->t('system', 'email_address');
        $contact[0]['value'] = empty($_POST) === false ? $_POST['mail'] : '';
        $contact[0]['maxlength'] = '120';
        $contact[1]['name'] = 'website';
        $contact[1]['lang'] = $this->lang->t('system', 'website');
        $contact[1]['value'] = empty($_POST) === false ? $_POST['website'] : '';
        $contact[1]['maxlength'] = '120';
        $contact[2]['name'] = 'icq';
        $contact[2]['lang'] = $this->lang->t('users', 'icq');
        $contact[2]['value'] = empty($_POST) === false ? $_POST['icq'] : '';
        $contact[2]['maxlength'] = '9';
        $contact[3]['name'] = 'skype';
        $contact[3]['lang'] = $this->lang->t('users', 'skype');
        $contact[3]['value'] = empty($_POST) === false ? $_POST['skype'] : '';
        $contact[3]['maxlength'] = '28';
        $this->view->assign('contact', $contact);

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

        $this->view->assign('form', array_merge($defaults, $_POST));

        $this->secureHelpers->generateFormToken($this->request->query);
    }

    public function actionDelete()
    {
        $items = $this->_deleteItem();

        if ($this->request->action === 'confirmed') {
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
        } elseif (is_string($items)) {
            throw new Core\Exceptions\ResultNotExists();
        }
    }

    public function actionEdit()
    {
        if ($this->get('core.validator.rules.misc')->isNumber($this->request->id) === true && $this->usersModel->resultExists($this->request->id) === true) {
            $user = $this->auth->getUserInfo($this->request->id);

            $this->breadcrumb->setTitlePostfix($user['nickname']);

            if (empty($_POST) === false) {
                $this->_editPost($_POST);
            }

            // Zugriffslevel holen
            $roles = $this->acl->getAllRoles();
            $c_roles = count($roles);
            $userRoles = $this->acl->getUserRoleIds($this->request->id);
            for ($i = 0; $i < $c_roles; ++$i) {
                $roles[$i]['name'] = str_repeat('&nbsp;&nbsp;', $roles[$i]['level']) . $roles[$i]['name'];
                $roles[$i]['selected'] = $this->formsHelpers->selectEntry('roles', $roles[$i]['id'], in_array($roles[$i]['id'], $userRoles) ? $roles[$i]['id'] : '');
            }
            $this->view->assign('roles', $roles);

            // Super User
            $langSuperUser = [$this->lang->t('system', 'yes'), $this->lang->t('system', 'no')];
            $this->view->assign('super_user', $this->formsHelpers->selectGenerator('super_user', [1, 0], $langSuperUser, $user['super_user'], 'checked'));

            // Sprache
            $this->view->assign('languages', $this->lang->getLanguages(isset($_POST['language']) ? $_POST['language'] : $user['language']));

            // Einträge pro Seite
            $this->view->assign('entries', $this->formsHelpers->recordsPerPage((int)$user['entries']));

            // Zeitzonen
            $this->view->assign('time_zones', $this->date->getTimeZones($user['time_zone']));

            // Geschlecht
            $lang_gender = [
                $this->lang->t('users', 'gender_not_specified'),
                $this->lang->t('users', 'gender_female'),
                $this->lang->t('users', 'gender_male')
            ];
            $this->view->assign('gender', $this->formsHelpers->selectGenerator('gender', [1, 2, 3], $lang_gender, $user['gender']));

            // Geburtstag
            $datepickerParams = ['constrainInput' => 'true', 'changeMonth' => 'true', 'changeYear' => 'true', 'yearRange' => '\'-50:+0\''];
            $this->view->assign('birthday_datepicker', $this->date->datepicker('birthday', $user['birthday'], 'Y-m-d', $datepickerParams, 0, false, true));

            // Kontaktangaben
            $contact = [];
            $contact[0]['name'] = 'mail';
            $contact[0]['lang'] = $this->lang->t('system', 'email_address');
            $contact[0]['value'] = empty($_POST) === false ? $_POST['mail'] : $user['mail'];
            $contact[0]['maxlength'] = '120';
            $contact[1]['name'] = 'website';
            $contact[1]['lang'] = $this->lang->t('system', 'website');
            $contact[1]['value'] = empty($_POST) === false ? $_POST['website'] : $user['website'];
            $contact[1]['maxlength'] = '120';
            $contact[2]['name'] = 'icq';
            $contact[2]['lang'] = $this->lang->t('users', 'icq');
            $contact[2]['value'] = empty($_POST) === false ? $_POST['icq'] : $user['icq'];
            $contact[2]['maxlength'] = '9';
            $contact[3]['name'] = 'skype';
            $contact[3]['lang'] = $this->lang->t('users', 'skype');
            $contact[3]['value'] = empty($_POST) === false ? $_POST['skype'] : $user['skype'];
            $contact[3]['maxlength'] = '28';
            $this->view->assign('contact', $contact);

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

            $this->view->assign('form', array_merge($user, $_POST));

            $this->secureHelpers->generateFormToken($this->request->query);
        } else {
            throw new Core\Exceptions\ResultNotExists();
        }
    }

    public function actionSettings()
    {
        if (empty($_POST) === false) {
            $this->_settingsPost($_POST);
        }

        $settings = $this->usersConfig->getSettings();

        $lang_languages = [$this->lang->t('system', 'yes'), $this->lang->t('system', 'no')];
        $this->view->assign('languages', $this->formsHelpers->selectGenerator('language_override', [1, 0], $lang_languages, $settings['language_override'], 'checked'));

        $lang_entries = [$this->lang->t('system', 'yes'), $this->lang->t('system', 'no')];
        $this->view->assign('entries', $this->formsHelpers->selectGenerator('entries_override', [1, 0], $lang_entries, $settings['entries_override'], 'checked'));

        $lang_registration = [$this->lang->t('system', 'yes'), $this->lang->t('system', 'no')];
        $this->view->assign('registration', $this->formsHelpers->selectGenerator('enable_registration', [1, 0], $lang_registration, $settings['enable_registration'], 'checked'));

        $this->view->assign('form', array_merge(['mail' => $settings['mail']], $_POST));

        $this->secureHelpers->generateFormToken($this->request->query);
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
    private function _createPost($formData)
    {
        try {
            $this->usersValidator->validate($formData);

            $securityHelper = $this->get('core.helpers.secure');
            $salt = $securityHelper->salt(12);

            $insertValues = [
                'id' => '',
                'super_user' => (int)$formData['super_user'],
                'nickname' => Core\Functions::strEncode($formData['nickname']),
                'pwd' => $securityHelper->generateSaltedPassword($salt, $formData['pwd']) . ':' . $salt,
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
            foreach ($formData['roles'] as $row) {
                $this->permissionsModel->insert(['user_id' => $lastId, 'role_id' => $row], Permissions\Model::TABLE_NAME_USER_ROLES);
            }

            $this->secureHelpers->unsetFormToken($this->request->query);

            $this->redirectMessages()->setMessage($lastId, $this->lang->t('system', $lastId !== false ? 'create_success' : 'create_error'));
        } catch (Core\Exceptions\InvalidFormToken $e) {
            $this->redirectMessages()->setMessage(false, $e->getMessage());
        } catch (Core\Exceptions\ValidationFailed $e) {
            $this->view->assign('error_msg', $this->get('core.helpers.alerts')->errorBox($e->getMessage()));
        }
    }

    /**
     * @param array $formData
     */
    private function _editPost(array $formData)
    {
        try {
            $this->usersValidator->validate($formData, (int) $this->request->id);

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

            $this->permissionsModel->delete($this->request->id, 'user_id', Permissions\Model::TABLE_NAME_USER_ROLES);
            foreach ($formData['roles'] as $row) {
                $this->permissionsModel->insert(['user_id' => $this->request->id, 'role_id' => $row], Permissions\Model::TABLE_NAME_USER_ROLES);
            }

            // Neues Passwort
            if (!empty($formData['new_pwd']) && !empty($formData['new_pwd_repeat'])) {
                $salt = $this->secureHelpers->salt(12);
                $newPassword = $this->secureHelpers->generateSaltedPassword($salt, $formData['new_pwd']);
                $updateValues['pwd'] = $newPassword . ':' . $salt;
            }

            $bool = $this->usersModel->update($updateValues, $this->request->id);

            // Falls sich der User selbst bearbeitet hat, Cookie aktualisieren
            if ($this->request->id == $this->auth->getUserId()) {
                $cookieArray = explode('|', base64_decode($_COOKIE['ACP3_AUTH']));
                $this->auth->setCookie($formData['nickname'], isset($newPassword) ? $newPassword : $cookieArray[1], 3600);
            }

            $this->secureHelpers->unsetFormToken($this->request->query);

            $this->redirectMessages()->setMessage($bool, $this->lang->t('system', $bool !== false ? 'edit_success' : 'edit_error'));
        } catch (Core\Exceptions\InvalidFormToken $e) {
            $this->redirectMessages()->setMessage(false, $e->getMessage());
        } catch (Core\Exceptions\ValidationFailed $e) {
            $this->view->assign('error_msg', $this->get('core.helpers.alerts')->errorBox($e->getMessage()));
        }
    }

    /**
     * @param array $formData
     */
    private function _settingsPost(array $formData)
    {
        try {
            $this->usersValidator->validateSettings($formData);

            $data = [
                'enable_registration' => $formData['enable_registration'],
                'entries_override' => $formData['entries_override'],
                'language_override' => $formData['language_override'],
                'mail' => $formData['mail']
            ];
            $bool = $this->usersConfig->setSettings($data);

            $this->secureHelpers->unsetFormToken($this->request->query);

            $this->redirectMessages()->setMessage($bool, $this->lang->t('system', $bool === true ? 'settings_success' : 'settings_error'));
        } catch (Core\Exceptions\InvalidFormToken $e) {
            $this->redirectMessages()->setMessage(false, $e->getMessage());
        } catch (Core\Exceptions\ValidationFailed $e) {
            $this->view->assign('error_msg', $this->get('core.helpers.alerts')->errorBox($e->getMessage()));
        }
    }
}
