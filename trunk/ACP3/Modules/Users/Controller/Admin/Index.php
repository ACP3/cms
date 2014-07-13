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
     * @var \ACP3\Core\ACL
     */
    protected $acl;
    /**
     * @var Core\Date
     */
    protected $date;
    /**
     * @var \Doctrine\DBAL\Connection
     */
    protected $db;
    /**
     * @var Users\Model
     */
    protected $usersModel;
    /**
     * @var Permissions\Model
     */
    protected $permissionsModel;

    public function __construct(
        Core\Auth $auth,
        Core\Breadcrumb $breadcrumb,
        Core\Lang $lang,
        Core\URI $uri,
        Core\View $view,
        Core\SEO $seo,
        Core\Modules $modules,
        Core\Validate $validate,
        Core\Session $session,
        Core\ACL $acl,
        Core\Date $date,
        \Doctrine\DBAL\Connection $db,
        Users\Model $usersModel,
        Permissions\Model $permissionsModel)
    {
        parent::__construct($auth, $breadcrumb, $lang, $uri, $view, $seo, $modules, $validate, $session);

        $this->acl = $acl;
        $this->date = $date;
        $this->db = $db;
        $this->usersModel = $usersModel;
        $this->permissionsModel = $permissionsModel;
    }

    public function actionCreate()
    {
        if (empty($_POST) === false) {
            try {
                $validator = $this->get('users.validator');
                $validator->validateCreate($_POST);

                $securityHelper = $this->get('core.helpers.secure');
                $salt = $securityHelper->salt(12);

                $insertValues = array(
                    'id' => '',
                    'super_user' => (int)$_POST['super_user'],
                    'nickname' => Core\Functions::strEncode($_POST['nickname']),
                    'pwd' => $securityHelper->generateSaltedPassword($salt, $_POST['pwd']) . ':' . $salt,
                    'realname' => Core\Functions::strEncode($_POST['realname']),
                    'gender' => (int)$_POST['gender'],
                    'birthday' => $_POST['birthday'],
                    'birthday_display' => (int)$_POST['birthday_display'],
                    'mail' => $_POST['mail'],
                    'mail_display' => isset($_POST['mail_display']) ? 1 : 0,
                    'website' => Core\Functions::strEncode($_POST['website']),
                    'icq' => $_POST['icq'],
                    'skype' => Core\Functions::strEncode($_POST['skype']),
                    'street' => Core\Functions::strEncode($_POST['street']),
                    'house_number' => Core\Functions::strEncode($_POST['house_number']),
                    'zip' => Core\Functions::strEncode($_POST['zip']),
                    'city' => Core\Functions::strEncode($_POST['city']),
                    'address_display' => isset($_POST['address_display']) ? 1 : 0,
                    'country' => Core\Functions::strEncode($_POST['country']),
                    'country_display' => isset($_POST['country_display']) ? 1 : 0,
                    'date_format_long' => Core\Functions::strEncode($_POST['date_format_long']),
                    'date_format_short' => Core\Functions::strEncode($_POST['date_format_short']),
                    'time_zone' => $_POST['date_time_zone'],
                    'language' => $_POST['language'],
                    'entries' => (int)$_POST['entries'],
                    'draft' => '',
                    'registration_date' => $this->date->getCurrentDateTime(),
                );

                $this->db->beginTransaction();
                try {
                    $lastId = $this->usersModel->insert($insertValues);
                    foreach ($_POST['roles'] as $row) {
                        $this->permissionsModel->insert(array('user_id' => $lastId, 'role_id' => $row), Permissions\Model::TABLE_NAME_USER_ROLES);
                    }
                    $this->db->commit();
                } catch (\Exception $e) {
                    $this->db->rollback();
                    $lastId = false;
                }

                $this->session->unsetFormToken();

                $this->redirectMessages()->setMessage($lastId, $this->lang->t('system', $lastId !== false ? 'create_success' : 'create_error'), 'acp/users');
            } catch (Core\Exceptions\InvalidFormToken $e) {
                $this->redirectMessages()->setMessage(false, $e->getMessage(), 'acp/users');
            } catch (Core\Exceptions\ValidationFailed $e) {
                $alerts = new Core\Helpers\Alerts($this->uri, $this->view);
                $this->view->assign('error_msg', $alerts->errorBox($e->getMessage()));
            }
        }

        // Zugriffslevel holen
        $roles = $this->acl->getAllRoles();
        $c_roles = count($roles);
        for ($i = 0; $i < $c_roles; ++$i) {
            $roles[$i]['name'] = str_repeat('&nbsp;&nbsp;', $roles[$i]['level']) . $roles[$i]['name'];
            $roles[$i]['selected'] = Core\Functions::selectEntry('roles', $roles[$i]['id']);
        }
        $this->view->assign('roles', $roles);

        // Super User
        $lang_super_user = array($this->lang->t('system', 'yes'), $this->lang->t('system', 'no'));
        $this->view->assign('super_user', Core\Functions::selectGenerator('super_user', array(1, 0), $lang_super_user, 0, 'checked'));

        // Sprache
        $this->view->assign('languages', $this->lang->getLanguages(isset($_POST['language']) ? $_POST['language'] : CONFIG_LANG));

        // Einträge pro Seite
        $this->view->assign('entries', Core\Functions::recordsPerPage(CONFIG_ENTRIES));

        // Zeitzonen
        $this->view->assign('time_zones', $this->date->getTimeZones(CONFIG_DATE_TIME_ZONE));

        // Geschlecht
        $lang_gender = array(
            $this->lang->t('users', 'gender_not_specified'),
            $this->lang->t('users', 'gender_female'),
            $this->lang->t('users', 'gender_male')
        );
        $this->view->assign('gender', Core\Functions::selectGenerator('gender', array(1, 2, 3), $lang_gender, ''));

        // Geburtstag
        $datepickerParams = array(
            'constrainInput' => 'true',
            'changeMonth' => 'true',
            'changeYear' => 'true',
            'yearRange' => '\'-50:+0\''
        );
        $this->view->assign('birthday_datepicker', $this->date->datepicker('birthday', '', 'Y-m-d', $datepickerParams, 0, false, true));

        // Kontaktangaben
        $contact = array();
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
        $countries_select = array();
        foreach ($countries as $key => $value) {
            $countries_select[] = array(
                'value' => $key,
                'lang' => $value,
                'selected' => Core\Functions::selectEntry('countries', $key),
            );
        }
        $this->view->assign('countries', $countries_select);

        $lang_mail_display = array($this->lang->t('system', 'yes'), $this->lang->t('system', 'no'));
        $this->view->assign('mail_display', Core\Functions::selectGenerator('mail_display', array(1, 0), $lang_mail_display, 0, 'checked'));

        $lang_address_display = array($this->lang->t('system', 'yes'), $this->lang->t('system', 'no'));
        $this->view->assign('address_display', Core\Functions::selectGenerator('address_display', array(1, 0), $lang_address_display, 0, 'checked'));

        $lang_country_display = array($this->lang->t('system', 'yes'), $this->lang->t('system', 'no'));
        $this->view->assign('country_display', Core\Functions::selectGenerator('country_display', array(1, 0), $lang_country_display, 0, 'checked'));

        $lang_birthday_display = array(
            $this->lang->t('users', 'birthday_hide'),
            $this->lang->t('users', 'birthday_display_completely'),
            $this->lang->t('users', 'birthday_hide_year')
        );
        $this->view->assign('birthday_display', Core\Functions::selectGenerator('birthday_display', array(0, 1, 2), $lang_birthday_display, 0, 'checked'));

        $defaults = array(
            'nickname' => '',
            'realname' => '',
            'mail' => '',
            'website' => '',
            'street' => '',
            'house_number' => '',
            'zip' => '',
            'city' => '',
            'date_format_long' => CONFIG_DATE_FORMAT_LONG,
            'date_format_short' => CONFIG_DATE_FORMAT_SHORT
        );

        $this->view->assign('form', array_merge($defaults, $_POST));

        $this->session->generateFormToken();
    }

    public function actionDelete()
    {
        $items = $this->_deleteItem('acp/users/index/delete', 'acp/users');

        if ($this->uri->action === 'confirmed') {
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

            $this->redirectMessages()->setMessage($bool, $text, $selfDelete === true ? ROOT_DIR : 'acp/users');
        } elseif (is_string($items)) {
            throw new Core\Exceptions\ResultNotExists();
        }
    }

    public function actionEdit()
    {
        if ($this->get('core.validate')->isNumber($this->uri->id) === true && $this->usersModel->resultExists($this->uri->id) === true) {
            $user = $this->auth->getUserInfo($this->uri->id);

            if (empty($_POST) === false) {
                try {
                    $validator = $this->get('users.validator');
                    $validator->validateEdit($_POST);

                    $updateValues = array(
                        'super_user' => (int)$_POST['super_user'],
                        'nickname' => Core\Functions::strEncode($_POST['nickname']),
                        'realname' => Core\Functions::strEncode($_POST['realname']),
                        'gender' => (int)$_POST['gender'],
                        'birthday' => $_POST['birthday'],
                        'birthday_display' => (int)$_POST['birthday_display'],
                        'mail' => $_POST['mail'],
                        'mail_display' => (int)$_POST['mail_display'],
                        'website' => Core\Functions::strEncode($_POST['website']),
                        'icq' => $_POST['icq'],
                        'skype' => Core\Functions::strEncode($_POST['skype']),
                        'street' => Core\Functions::strEncode($_POST['street']),
                        'house_number' => Core\Functions::strEncode($_POST['house_number']),
                        'zip' => Core\Functions::strEncode($_POST['zip']),
                        'city' => Core\Functions::strEncode($_POST['city']),
                        'address_display' => (int)$_POST['address_display'],
                        'country' => Core\Functions::strEncode($_POST['country']),
                        'country_display' => (int)$_POST['country_display'],
                        'date_format_long' => Core\Functions::strEncode($_POST['date_format_long']),
                        'date_format_short' => Core\Functions::strEncode($_POST['date_format_short']),
                        'time_zone' => $_POST['date_time_zone'],
                        'language' => $_POST['language'],
                        'entries' => (int)$_POST['entries'],
                    );

                    // Rollen aktualisieren
                    $this->db->beginTransaction();
                    try {
                        $this->permissionsModel->delete(array('user_id' => $this->uri->id), Permissions\Model::TABLE_NAME_USER_ROLES);
                        foreach ($_POST['roles'] as $row) {
                            $this->permissionsModel->insert(array('user_id' => $this->uri->id, 'role_id' => $row), Permissions\Model::TABLE_NAME_USER_ROLES);
                        }
                        $this->db->commit();
                    } catch (\Exception $e) {
                        $this->db->rollback();
                    }

                    // Neues Passwort
                    if (!empty($_POST['new_pwd']) && !empty($_POST['new_pwd_repeat'])) {
                        $securityHelper = new Core\Helpers\Secure();

                        $salt = $securityHelper->salt(12);
                        $newPassword = $securityHelper->generateSaltedPassword($salt, $_POST['new_pwd']);
                        $updateValues['pwd'] = $newPassword . ':' . $salt;
                    }

                    $bool = $this->usersModel->update($updateValues, $this->uri->id);

                    // Falls sich der User selbst bearbeitet hat, Cookie aktualisieren
                    if ($this->uri->id == $this->auth->getUserId()) {
                        $cookieArray = explode('|', base64_decode($_COOKIE['ACP3_AUTH']));
                        $this->auth->setCookie($_POST['nickname'], isset($newPassword) ? $newPassword : $cookieArray[1], 3600);
                    }

                    $this->session->unsetFormToken();

                    $this->redirectMessages()->setMessage($bool, $this->lang->t('system', $bool !== false ? 'edit_success' : 'edit_error'), 'acp/users');
                } catch (Core\Exceptions\InvalidFormToken $e) {
                    $this->redirectMessages()->setMessage(false, $e->getMessage(), 'acp/users');
                } catch (Core\Exceptions\ValidationFailed $e) {
                    $alerts = new Core\Helpers\Alerts($this->uri, $this->view);
                    $this->view->assign('error_msg', $alerts->errorBox($e->getMessage()));
                }
            }
            // Zugriffslevel holen
            $roles = $this->acl->getAllRoles();
            $c_roles = count($roles);
            $userRoles = $this->acl->getUserRoles($this->uri->id);
            for ($i = 0; $i < $c_roles; ++$i) {
                $roles[$i]['name'] = str_repeat('&nbsp;&nbsp;', $roles[$i]['level']) . $roles[$i]['name'];
                $roles[$i]['selected'] = Core\Functions::selectEntry('roles', $roles[$i]['id'], in_array($roles[$i]['id'], $userRoles) ? $roles[$i]['id'] : '');
            }
            $this->view->assign('roles', $roles);

            // Super User
            $langSuperUser = array($this->lang->t('system', 'yes'), $this->lang->t('system', 'no'));
            $this->view->assign('super_user', Core\Functions::selectGenerator('super_user', array(1, 0), $langSuperUser, $user['super_user'], 'checked'));

            // Sprache
            $this->view->assign('languages', $this->lang->getLanguages(isset($_POST['language']) ? $_POST['language'] : $user['language']));

            // Einträge pro Seite
            $this->view->assign('entries', Core\Functions::recordsPerPage((int)$user['entries']));

            // Zeitzonen
            $this->view->assign('time_zones', Core\Date::getTimeZones($user['time_zone']));

            // Geschlecht
            $lang_gender = array(
                $this->lang->t('users', 'gender_not_specified'),
                $this->lang->t('users', 'gender_female'),
                $this->lang->t('users', 'gender_male')
            );
            $this->view->assign('gender', Core\Functions::selectGenerator('gender', array(1, 2, 3), $lang_gender, $user['gender']));

            // Geburtstag
            $datepickerParams = array('constrainInput' => 'true', 'changeMonth' => 'true', 'changeYear' => 'true', 'yearRange' => '\'-50:+0\'');
            $this->view->assign('birthday_datepicker', $this->date->datepicker('birthday', $user['birthday'], 'Y-m-d', $datepickerParams, 0, false, true));

            // Kontaktangaben
            $contact = array();
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
            $countries_select = array();
            foreach ($countries as $key => $value) {
                $countries_select[] = array(
                    'value' => $key,
                    'lang' => $value,
                    'selected' => Core\Functions::selectEntry('countries', $key, $user['country']),
                );
            }
            $this->view->assign('countries', $countries_select);

            $lang_mail_display = array($this->lang->t('system', 'yes'), $this->lang->t('system', 'no'));
            $this->view->assign('mail_display', Core\Functions::selectGenerator('mail_display', array(1, 0), $lang_mail_display, $user['mail_display'], 'checked'));

            $lang_address_display = array($this->lang->t('system', 'yes'), $this->lang->t('system', 'no'));
            $this->view->assign('address_display', Core\Functions::selectGenerator('address_display', array(1, 0), $lang_address_display, $user['address_display'], 'checked'));

            $lang_country_display = array($this->lang->t('system', 'yes'), $this->lang->t('system', 'no'));
            $this->view->assign('country_display', Core\Functions::selectGenerator('country_display', array(1, 0), $lang_country_display, $user['country_display'], 'checked'));

            $lang_birthday_display = array(
                $this->lang->t('users', 'birthday_hide'),
                $this->lang->t('users', 'birthday_display_completely'),
                $this->lang->t('users', 'birthday_hide_year')
            );
            $this->view->assign('birthday_display', Core\Functions::selectGenerator('birthday_display', array(0, 1, 2), $lang_birthday_display, $user['birthday_display'], 'checked'));

            $this->view->assign('form', array_merge($user, $_POST));

            $this->session->generateFormToken();
        } else {
            throw new Core\Exceptions\ResultNotExists();
        }
    }

    public function actionSettings()
    {
        $config = new Core\Config($this->db, 'users');

        if (empty($_POST) === false) {
            try {
                $validator = $this->get('users.validator');
                $validator->validateSettings($_POST);

                $data = array(
                    'enable_registration' => $_POST['enable_registration'],
                    'entries_override' => $_POST['entries_override'],
                    'language_override' => $_POST['language_override'],
                    'mail' => $_POST['mail']
                );
                $bool = $config->setSettings($data);

                $this->session->unsetFormToken();

                $this->redirectMessages()->setMessage($bool, $this->lang->t('system', $bool === true ? 'settings_success' : 'settings_error'), 'acp/users');
            } catch (Core\Exceptions\InvalidFormToken $e) {
                $this->redirectMessages()->setMessage(false, $e->getMessage(), 'acp/users');
            } catch (Core\Exceptions\ValidationFailed $e) {
                $alerts = new Core\Helpers\Alerts($this->uri, $this->view);
                $this->view->assign('error_msg', $alerts->errorBox($e->getMessage()));
            }
        }

        $settings = $config->getSettings();

        $lang_languages = array($this->lang->t('system', 'yes'), $this->lang->t('system', 'no'));
        $this->view->assign('languages', Core\Functions::selectGenerator('language_override', array(1, 0), $lang_languages, $settings['language_override'], 'checked'));

        $lang_entries = array($this->lang->t('system', 'yes'), $this->lang->t('system', 'no'));
        $this->view->assign('entries', Core\Functions::selectGenerator('entries_override', array(1, 0), $lang_entries, $settings['entries_override'], 'checked'));

        $lang_registration = array($this->lang->t('system', 'yes'), $this->lang->t('system', 'no'));
        $this->view->assign('registration', Core\Functions::selectGenerator('enable_registration', array(1, 0), $lang_registration, $settings['enable_registration'], 'checked'));

        $this->view->assign('form', array_merge(array('mail' => $settings['mail']), $_POST));

        $this->session->generateFormToken();
    }

    public function actionIndex()
    {
        $this->redirectMessages()->getMessage();

        $users = $this->usersModel->getAllInAcp();
        $c_users = count($users);

        if ($c_users > 0) {
            $canDelete = $this->modules->hasPermission('admin/users/index/delete');
            $config = array(
                'element' => '#acp-table',
                'sort_col' => $canDelete === true ? 1 : 0,
                'sort_dir' => 'asc',
                'hide_col_sort' => $canDelete === true ? 0 : ''
            );
            $this->appendContent($this->get('core.functions')->dataTable($config));

            for ($i = 0; $i < $c_users; ++$i) {
                $users[$i]['roles'] = implode(', ', $this->acl->getUserRoles($users[$i]['id'], 2));
            }
            $this->view->assign('users', $users);
            $this->view->assign('can_delete', $canDelete);
        }
    }

}
