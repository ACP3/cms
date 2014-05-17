<?php

namespace ACP3\Modules\Newsletter;

use ACP3\Core;

/**
 * Description of Model
 *
 * @author Tino Goratsch
 */
class Model extends Core\Model
{

    const TABLE_NAME = 'newsletters';
    const TABLE_NAME_ACCOUNTS = 'newsletter_accounts';

    /**
     * @var \ACP3\Core\Auth
     */
    private $auth;

    public function __construct(\Doctrine\DBAL\Connection $db, Core\Lang $lang, Core\Auth $auth)
    {
        parent::__construct($db, $lang);

        $this->auth = $auth;
    }

    public function newsletterExists($id, $status = '')
    {
        $where = empty($status) === false ? ' AND status = :status' : '';
        return (int)$this->db->fetchAssoc('SELECT COUNT(*) FROM ' . $this->prefix . static::TABLE_NAME . ' WHERE id = :id' . $where, array('id' => $id, 'status' => $status)) > 0 ? true : false;
    }

    public function accountExists($emailAddress, $hash = '')
    {
        $where = empty($hash) === false ? ' AND hash = :hash' : '';
        return $this->db->fetchColumn('SELECT COUNT(*) FROM ' . $this->prefix . static::TABLE_NAME_ACCOUNTS . ' WHERE mail = :mail' . $where, array('mail' => $emailAddress, 'hash' => $hash)) > 0 ? true : false;
    }

    public function getOneById($id, $status = '')
    {
        $where = empty($status) === false ? ' AND status = :status' : '';
        return $this->db->fetchAssoc('SELECT * FROM ' . $this->prefix . static::TABLE_NAME . ' WHERE id = :id' . $where, array('id' => $id, 'status' => $status));
    }

    public function countAll($status = '')
    {
        $where = empty($time) === false ? ' WHERE status = :status' : '';
        return $this->db->fetchColumn('SELECT COUNT(*) FROM ' . $this->prefix . static::TABLE_NAME . $where, array('status' => $status));
    }

    public function getAll($status = '', $limitStart = '', $resultsPerPage = '')
    {
        $where = empty($status) === false ? ' WHERE status = :status' : '';
        $limitStmt = $this->_buildLimitStmt($limitStart, $resultsPerPage);
        return $this->db->fetchAll('SELECT * FROM ' . $this->prefix . static::TABLE_NAME . $where . ' ORDER BY date DESC' . $limitStmt, array('status' => $status));
    }

    public function getAllInAcp()
    {
        return $this->db->fetchAll('SELECT * FROM ' . $this->prefix . static::TABLE_NAME . ' ORDER BY date DESC');
    }

    public function getAllAccounts()
    {
        return $this->db->fetchAll('SELECT * FROM ' . $this->prefix . static::TABLE_NAME_ACCOUNTS . ' ORDER BY id DESC');
    }

    public function getAllActiveAccount()
    {
        return $this->db->fetchAll('SELECT * FROM ' . $this->prefix . static::TABLE_NAME_ACCOUNTS . ' WHERE hash = "" ORDER BY id DESC');
    }

    public function validate(array $formData)
    {
        $this->validateFormKey();

        $errors = array();
        if (strlen($formData['title']) < 3) {
            $errors['title'] = $this->lang->t('newsletter', 'subject_to_short');
        }
        if (strlen($formData['text']) < 3) {
            $errors['text'] = $this->lang->t('newsletter', 'text_to_short') . strlen($formData['text']);
        }

        if (!empty($errors)) {
            throw new Core\Exceptions\ValidationFailed(Core\Functions::errorBox($errors));
        }
    }

    public function validateSubscribe(array $formData)
    {
        $this->validateFormKey();

        $errors = array();
        if (Core\Validate::email($formData['mail']) === false) {
            $errors['mail'] = $this->lang->t('system', 'wrong_email_format');
        }
        if (Core\Validate::email($formData['mail']) && $this->accountExists($formData['mail']) === true) {
            $errors['mail'] = $this->lang->t('newsletter', 'account_exists');
        }
        if (Core\Modules::hasPermission('frontend/captcha/index/image') === true && $this->auth->isUser() === false && Core\Validate::captcha($formData['captcha']) === false) {
            $errors['captcha'] = $this->lang->t('captcha', 'invalid_captcha_entered');
        }

        if (!empty($errors)) {
            throw new Core\Exceptions\ValidationFailed(Core\Functions::errorBox($errors));
        }
    }

    public function validateUnsubscribe(array $formData)
    {
        $this->validateFormKey();

        $errors = array();
        if (Core\Validate::email($formData['mail']) === false) {
            $errors[] = $this->lang->t('system', 'wrong_email_format');
        }
        if (Core\Validate::email($formData['mail']) && $this->accountExists($formData['mail']) === false) {
            $errors[] = $this->lang->t('newsletter', 'account_not_exists');
        }
        if (Core\Modules::hasPermission('frontend/captcha/index/image') === true && $this->auth->isUser() === false && Core\Validate::captcha($formData['captcha']) === false) {
            $errors[] = $this->lang->t('captcha', 'invalid_captcha_entered');
        }

        if (!empty($errors)) {
            throw new Core\Exceptions\ValidationFailed(Core\Functions::errorBox($errors));
        }
    }

    public function validateSettings(array $formData)
    {
        $this->validateFormKey();

        $errors = array();
        if (Core\Validate::email($formData['mail']) === false) {
            $errors['mail'] = $this->lang->t('system', 'wrong_email_format');
        }

        if (!empty($errors)) {
            throw new Core\Exceptions\ValidationFailed(Core\Functions::errorBox($errors));
        }
    }

    public function validateActivate($mail, $hash)
    {
        $errors = array();
        if ($this->accountExists($mail, $hash) === false) {
            $errors[] = $this->lang->t('newsletter', 'account_not_exists');
        }

        if (!empty($errors)) {
            throw new Core\Exceptions\ValidationFailed(Core\Functions::errorBox($errors));
        }
    }

}
