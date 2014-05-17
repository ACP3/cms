<?php

namespace ACP3\Modules\Users;

use ACP3\Core;

/**
 * Description of Model
 *
 * @author Tino Goratsch
 */
class Model extends Core\Model
{

    const TABLE_NAME = 'users';

    /**
     * @var \ACP3\Core\Auth
     */
    private $auth;
    /**
     * @var \ACP3\Core\URI
     */
    private $uri;

    public function __construct(\Doctrine\DBAL\Connection $db, Core\Lang $lang, Core\Auth $auth, Core\URI $uri)
    {
        parent::__construct($db, $lang);

        $this->auth = $auth;
        $this->uri = $uri;
    }

    public function resultExists($id)
    {
        return (int)$this->db->fetchColumn('SELECT COUNT(*) FROM ' . $this->prefix . static::TABLE_NAME . ' WHERE id = :id', array('id' => $id)) > 0 ? true : false;
    }

    /**
     * Überprüft, ob der übergebene Username bereits existiert
     *
     * @param string $nickname
     *  Der zu überprüfende Nickname
     * @param string $id
     * @return boolean
     */
    public function resultExistsByUserName($nickname, $id = '')
    {
        if (Core\Validate::isNumber($id) === true) {
            return !empty($nickname) && $this->db->fetchColumn('SELECT COUNT(*) FROM ' . $this->prefix . static::TABLE_NAME . ' WHERE id != ? AND nickname = ?', array($id, $nickname)) == 1 ? true : false;
        } else {
            return !empty($nickname) && $this->db->fetchColumn('SELECT COUNT(*) FROM ' . $this->prefix . static::TABLE_NAME . ' WHERE nickname = ?', array($nickname)) == 1 ? true : false;
        }
    }

    /**
     * Überprüft, ob die übergebene E-Mail-Adresse bereits existiert
     *
     * @param string $mail
     *  Die zu überprüfende E-Mail-Adresse
     * @param string $id
     * @return boolean
     */
    public function resultExistsByEmail($mail, $id = '')
    {
        if (Core\Validate::isNumber($id) === true) {
            return Core\Validate::email($mail) === true && $this->db->fetchColumn('SELECT COUNT(*) FROM ' . $this->prefix . static::TABLE_NAME . ' WHERE id != ? AND mail = ?', array($id, $mail)) > 0 ? true : false;
        } else {
            return Core\Validate::email($mail) === true && $this->db->fetchColumn('SELECT COUNT(*) FROM ' . $this->prefix . static::TABLE_NAME . ' WHERE mail = ?', array($mail)) > 0 ? true : false;
        }
    }

    public function getOneById($id)
    {
        return $this->db->fetchAssoc('SELECT * FROM ' . $this->prefix . static::TABLE_NAME . ' WHERE id = ?', array($id));
    }

    public function getOneByNickname($nickname)
    {
        return $this->db->fetchAssoc('SELECT * FROM ' . $this->prefix . static::TABLE_NAME . ' WHERE nickname = ?', array($nickname));
    }

    public function getOneByEmail($email)
    {
        return $this->db->fetchAssoc('SELECT * FROM ' . $this->prefix . static::TABLE_NAME . ' WHERE mail = ?', array($email));
    }

    public function countAll()
    {
        return count($this->getAll());
    }


    public function getAll($limitStart = '', $resultsPerPage = '')
    {
        $limitStmt = $this->_buildLimitStmt($limitStart, $resultsPerPage);
        return $this->db->fetchAll('SELECT * FROM ' . $this->prefix . static::TABLE_NAME . ' ORDER BY nickname ASC, id ASC' . $limitStmt);
    }

    public function getAllInAcp()
    {
        return $this->db->fetchAll('SELECT * FROM ' . $this->prefix . static::TABLE_NAME . ' ORDER BY nickname ASC');
    }

    public function validateSettings(array $formData)
    {
        $this->validateFormKey();

        $errors = array();
        if (!empty($formData['mail']) && Core\Validate::email($formData['mail']) === false) {
            $errors['mail'] = $this->lang->t('system', 'wrong_email_format');
        }
        if (!isset($formData['language_override']) || $formData['language_override'] != 1 && $formData['language_override'] != 0) {
            $errors[] = $this->lang->t('users', 'select_languages_override');
        }
        if (!isset($formData['entries_override']) || $formData['entries_override'] != 1 && $formData['entries_override'] != 0) {
            $errors[] = $this->lang->t('users', 'select_entries_override');
        }
        if (!isset($formData['enable_registration']) || $formData['enable_registration'] != 1 && $formData['enable_registration'] != 0) {
            $errors[] = $this->lang->t('users', 'select_enable_registration');
        }

        if (!empty($errors)) {
            throw new Core\Exceptions\ValidationFailed(Core\Functions::errorBox($errors));
        }
    }

    public function validateCreate(array $formData)
    {
        $this->validateFormKey();

        $errors = array();
        if (empty($formData['nickname'])) {
            $errors['nickname'] = $this->lang->t('system', 'name_to_short');
        }
        if (Core\Validate::gender($formData['gender']) === false) {
            $errors['gender'] = $this->lang->t('users', 'select_gender');
        }
        if (!empty($formData['birthday']) && Core\Validate::birthday($formData['birthday']) === false) {
            $errors[] = $this->lang->t('users', 'invalid_birthday');
        }
        if ($this->resultExistsByUserName($formData['nickname'])) {
            $errors['nickname'] = $this->lang->t('users', 'user_name_already_exists');
        }
        if (Core\Validate::email($formData['mail']) === false) {
            $errors['mail'] = $this->lang->t('system', 'wrong_email_format');
        }
        if ($this->resultExistsByEmail($formData['mail'])) {
            $errors['mail'] = $this->lang->t('users', 'user_email_already_exists');
        }
        if (empty($formData['roles']) || is_array($formData['roles']) === false || Core\Validate::aclRolesExist($formData['roles']) === false) {
            $errors['roles'] = $this->lang->t('users', 'select_access_level');
        }
        if (!isset($formData['super_user']) || ($formData['super_user'] != 1 && $formData['super_user'] != 0)) {
            $errors['super-user'] = $this->lang->t('users', 'select_super_user');
        }
        if ($this->lang->languagePackExists($formData['language']) === false) {
            $errors['language'] = $this->lang->t('users', 'select_language');
        }
        if (Core\Validate::isNumber($formData['entries']) === false) {
            $errors['entries'] = $this->lang->t('system', 'select_records_per_page');
        }
        if (empty($formData['date_format_long']) || empty($formData['date_format_short'])) {
            $errors[] = $this->lang->t('system', 'type_in_date_format');
        }
        if (Core\Validate::timeZone($formData['date_time_zone']) === false) {
            $errors['time-zone'] = $this->lang->t('system', 'select_time_zone');
        }
        if (!empty($formData['icq']) && Core\Validate::icq($formData['icq']) === false) {
            $errors['icq'] = $this->lang->t('users', 'invalid_icq_number');
        }
        if (in_array($formData['mail_display'], array(0, 1)) === false) {
            $errors[] = $this->lang->t('users', 'select_mail_display');
        }
        if (in_array($formData['address_display'], array(0, 1)) === false) {
            $errors[] = $this->lang->t('users', 'select_address_display');
        }
        if (in_array($formData['country_display'], array(0, 1)) === false) {
            $errors[] = $this->lang->t('users', 'select_country_display');
        }
        if (in_array($formData['birthday_display'], array(0, 1, 2)) === false) {
            $errors[] = $this->lang->t('users', 'select_birthday_display');
        }
        if (empty($_POST['pwd']) || empty($_POST['pwd_repeat']) || $_POST['pwd'] != $_POST['pwd_repeat']) {
            $errors[] = $this->lang->t('users', 'type_in_pwd');
        }

        if (!empty($errors)) {
            throw new Core\Exceptions\ValidationFailed(Core\Functions::errorBox($errors));
        }
    }

    public function validateEdit(array $formData)
    {
        $this->validateFormKey();

        $errors = array();
        if (empty($formData['nickname'])) {
            $errors['nickname'] = $this->lang->t('system', 'name_to_short');
        }
        if (Core\Validate::gender($formData['gender']) === false) {
            $errors['gender'] = $this->lang->t('users', 'select_gender');
        }
        if (!empty($formData['birthday']) && Core\Validate::birthday($formData['birthday']) === false) {
            $errors[] = $this->lang->t('users', 'invalid_birthday');
        }
        if ($this->resultExistsByUserName($formData['nickname'], $this->uri->id)) {
            $errors['nickname'] = $this->lang->t('users', 'user_name_already_exists');
        }
        if (Core\Validate::email($formData['mail']) === false) {
            $errors['mail'] = $this->lang->t('system', 'wrong_email_format');
        }
        if ($this->resultExistsByEmail($formData['mail'], $this->uri->id)) {
            $errors['mail'] = $this->lang->t('users', 'user_email_already_exists');
        }
        if (empty($formData['roles']) || is_array($formData['roles']) === false || Core\Validate::aclRolesExist($formData['roles']) === false) {
            $errors['roles'] = $this->lang->t('users', 'select_access_level');
        }
        if (!isset($formData['super_user']) || ($formData['super_user'] != 1 && $formData['super_user'] != 0)) {
            $errors['super-user'] = $this->lang->t('users', 'select_super_user');
        }
        if ($this->lang->languagePackExists($formData['language']) === false) {
            $errors['language'] = $this->lang->t('users', 'select_language');
        }
        if (Core\Validate::isNumber($formData['entries']) === false) {
            $errors['entries'] = $this->lang->t('system', 'select_records_per_page');
        }
        if (empty($formData['date_format_long']) || empty($formData['date_format_short'])) {
            $errors[] = $this->lang->t('system', 'type_in_date_format');
        }
        if (Core\Validate::timeZone($formData['date_time_zone']) === false) {
            $errors['time-zone'] = $this->lang->t('system', 'select_time_zone');
        }
        if (!empty($formData['icq']) && Core\Validate::icq($formData['icq']) === false) {
            $errors['icq'] = $this->lang->t('users', 'invalid_icq_number');
        }
        if (in_array($formData['mail_display'], array(0, 1)) === false) {
            $errors[] = $this->lang->t('users', 'select_mail_display');
        }
        if (in_array($formData['address_display'], array(0, 1)) === false) {
            $errors[] = $this->lang->t('users', 'select_address_display');
        }
        if (in_array($formData['country_display'], array(0, 1)) === false) {
            $errors[] = $this->lang->t('users', 'select_country_display');
        }
        if (in_array($formData['birthday_display'], array(0, 1, 2)) === false) {
            $errors[] = $this->lang->t('users', 'select_birthday_display');
        }
        if (!empty($formData['new_pwd']) && !empty($formData['new_pwd_repeat']) && $formData['new_pwd'] != $formData['new_pwd_repeat']) {
            $errors[] = $this->lang->t('users', 'type_in_pwd');
        }

        if (!empty($errors)) {
            throw new Core\Exceptions\ValidationFailed(Core\Functions::errorBox($errors));
        }
    }

    public function validateEditProfile(array $formData)
    {
        $this->validateFormKey();

        $errors = array();
        if (empty($formData['nickname'])) {
            $errors['nnickname'] = $this->lang->t('system', 'name_to_short');
        }
        if ($this->resultExistsByUserName($formData['nickname'], $this->auth->getUserId()) === true) {
            $errors['nickname'] = $this->lang->t('users', 'user_name_already_exists');
        }
        if (Core\Validate::gender($formData['gender']) === false) {
            $errors['gender'] = $this->lang->t('users', 'select_gender');
        }
        if (!empty($formData['birthday']) && Core\Validate::birthday($formData['birthday']) === false) {
            $errors[] = $this->lang->t('users', 'invalid_birthday');
        }
        if (Core\Validate::email($formData['mail']) === false) {
            $errors['mail'] = $this->lang->t('system', 'wrong_email_format');
        }
        if ($this->resultExistsByEmail($formData['mail'], $this->auth->getUserId()) === true) {
            $errors['mail'] = $this->lang->t('users', 'user_email_already_exists');
        }
        if (!empty($formData['icq']) && Core\Validate::icq($formData['icq']) === false) {
            $errors['icq'] = $this->lang->t('users', 'invalid_icq_number');
        }
        if (!empty($formData['new_pwd']) && !empty($formData['new_pwd_repeat']) && $formData['new_pwd'] != $formData['new_pwd_repeat']) {
            $errors[] = $this->lang->t('users', 'type_in_pwd');
        }

        if (!empty($errors)) {
            throw new Core\Exceptions\ValidationFailed(Core\Functions::errorBox($errors));
        }
    }

    public function validateUserSettings(array $formData, array $settings)
    {
        $this->validateFormKey();

        $errors = array();
        if ($settings['language_override'] == 1 && $this->lang->languagePackExists($formData['language']) === false) {
            $errors['language'] = $this->lang->t('users', 'select_language');
        }
        if ($settings['entries_override'] == 1 && Core\Validate::isNumber($formData['entries']) === false) {
            $errors['entries'] = $this->lang->t('system', 'select_records_per_page');
        }
        if (empty($formData['date_format_long']) || empty($formData['date_format_short'])) {
            $errors[] = $this->lang->t('system', 'type_in_date_format');
        }
        if (Core\Validate::timeZone($formData['date_time_zone']) === false) {
            $errors['time-zone'] = $this->lang->t('system', 'select_time_zone');
        }
        if (in_array($formData['mail_display'], array(0, 1)) === false) {
            $errors[] = $this->lang->t('users', 'select_mail_display');
        }
        if (in_array($formData['address_display'], array(0, 1)) === false) {
            $errors[] = $this->lang->t('users', 'select_address_display');
        }
        if (in_array($formData['country_display'], array(0, 1)) === false) {
            $errors[] = $this->lang->t('users', 'select_country_display');
        }
        if (in_array($formData['birthday_display'], array(0, 1, 2)) === false) {
            $errors[] = $this->lang->t('users', 'select_birthday_display');
        }

        if (!empty($errors)) {
            throw new Core\Exceptions\ValidationFailed(Core\Functions::errorBox($errors));
        }
    }

    public function validateForgotPassword(array $formData)
    {
        $this->validateFormKey();

        $errors = array();
        if (empty($formData['nick_mail'])) {
            $errors['nick-mail'] = $this->lang->t('users', 'type_in_nickname_or_email');
        } elseif (Core\Validate::email($formData['nick_mail']) === false && $this->resultExistsByUserName($formData['nick_mail']) === false) {
            $errors['nick-mail'] = $this->lang->t('users', 'user_not_exists');
        } elseif (Core\Validate::email($formData['nick_mail']) === true && $this->resultExistsByEmail($formData['nick_mail']) === false) {
            $errors['nick-mail'] = $this->lang->t('users', 'user_not_exists');
        }
        if (Core\Modules::hasPermission('frontend/captcha/index/image') === true && Core\Validate::captcha($formData['captcha']) === false) {
            $errors['captcha'] = $this->lang->t('captcha', 'invalid_captcha_entered');
        }

        if (!empty($errors)) {
            throw new Core\Exceptions\ValidationFailed(Core\Functions::errorBox($errors));
        }
    }

    public function validateRegistration(array $formData)
    {
        $this->validateFormKey();

        $errors = array();
        if (empty($formData['nickname'])) {
            $errors['nickname'] = $this->lang->t('system', 'name_to_short');
        }
        if ($this->resultExistsByUserName($formData['nickname']) === true) {
            $errors['nickname'] = $this->lang->t('users', 'user_name_already_exists');
        }
        if (Core\Validate::email($formData['mail']) === false) {
            $errors['mail'] = $this->lang->t('system', 'wrong_email_format');
        }
        if ($this->userEmailNameExists($formData['mail']) === true) {
            $errors['mail'] = $this->lang->t('users', 'user_email_already_exists');
        }
        if (empty($formData['pwd']) || empty($formData['pwd_repeat']) || $formData['pwd'] != $formData['pwd_repeat']) {
            $errors[] = $this->lang->t('users', 'type_in_pwd');
        }
        if (Core\Modules::hasPermission('frontend/captcha/index/image') === true && Core\Validate::captcha($formData['captcha']) === false) {
            $errors['captcha'] = $this->lang->t('captcha', 'invalid_captcha_entered');
        }

        if (!empty($errors)) {
            throw new Core\Exceptions\ValidationFailed(Core\Functions::errorBox($errors));
        }
    }
}
