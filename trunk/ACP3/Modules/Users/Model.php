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

    public function __construct(\Doctrine\DBAL\Connection $db, Core\Lang $lang, Core\Auth $auth)
    {
        parent::__construct($db, $lang);

        $this->auth = $auth;
    }

    public function resultExists($id)
    {
        return (int)$this->db->fetchColumn('SELECT COUNT(*) FROM ' . $this->prefix . static::TABLE_NAME . ' WHERE id = :id', array('id' => $id)) > 0 ? true : false;
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
        return $this->db->fetchAll('SELECT * FROM ' . $this->prefix . static::TABLE_NAME . ' ORDER BY id DESC');
    }

    public function validateSettings(array $formData)
    {
        $this->validateFormKey();

        $errors = array();

        if (!empty($errors)) {
            throw new Core\Exceptions\ValidationFailed(Core\Functions::errorBox($errors));
        }
    }

    public function validateProfile(array $formData)
    {
        $this->validateFormKey();

        $errors = array();
        if (empty($formData['nickname'])) {
            $errors['nnickname'] = $this->lang->t('system', 'name_to_short');
        }
        if (Helpers::userNameExists($formData['nickname'], $this->auth->getUserId()) === true) {
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
        if (Helpers::userEmailExists($formData['mail'], $this->auth->getUserId()) === true) {
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
        } elseif (Core\Validate::email($formData['nick_mail']) === false && Helpers::userNameExists($formData['nick_mail']) === false) {
            $errors['nick-mail'] = $this->lang->t('users', 'user_not_exists');
        } elseif (Core\Validate::email($formData['nick_mail']) === true && Helpers::userEmailExists($formData['nick_mail']) === false) {
            $errors['nick-mail'] = $this->lang->t('users', 'user_not_exists');
        }
        if (Core\Modules::hasPermission('captcha', 'image') === true && Core\Validate::captcha($formData['captcha']) === false) {
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
        if (Helpers::userNameExists($formData['nickname']) === true) {
            $errors['nickname'] = $this->lang->t('users', 'user_name_already_exists');
        }
        if (Core\Validate::email($formData['mail']) === false) {
            $errors['mail'] = $this->lang->t('system', 'wrong_email_format');
        }
        if (Helpers::userEmailExists($formData['mail']) === true) {
            $errors['mail'] = $this->lang->t('users', 'user_email_already_exists');
        }
        if (empty($formData['pwd']) || empty($formData['pwd_repeat']) || $formData['pwd'] != $formData['pwd_repeat']) {
            $errors[] = $this->lang->t('users', 'type_in_pwd');
        }
        if (Core\Modules::hasPermission('captcha', 'image') === true && Core\Validate::captcha($formData['captcha']) === false) {
            $errors['captcha'] = $this->lang->t('captcha', 'invalid_captcha_entered');
        }

        if (!empty($errors)) {
            throw new Core\Exceptions\ValidationFailed(Core\Functions::errorBox($errors));
        }
    }
}
