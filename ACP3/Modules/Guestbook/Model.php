<?php

namespace ACP3\Modules\Guestbook;

use ACP3\Core;

/**
 * Description of Model
 *
 * @author Tino Goratsch
 */
class Model extends Core\Model
{

    const TABLE_NAME = 'guestbook';

    /**
     * @var \ACP3\Core\Auth
     */
    protected $auth;
    /**
     * @var \ACP3\Core\Date
     */
    protected $date;

    public function __construct(\Doctrine\DBAL\Connection $db, Core\Lang $lang, Core\Date $date, Core\Auth $auth)
    {
        parent::__construct($db, $lang);

        $this->date = $date;
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

    public function countAll($notify = '')
    {
        return count($this->getAll($notify));
    }

    public function getLastDateFromIp($ipAddress)
    {
        return $this->db->fetchColumn('SELECT MAX(date) FROM ' . $this->prefix . static::TABLE_NAME . ' WHERE ip = ?', array($ipAddress));
    }

    public function getAll($notify = '', $limitStart = '', $resultsPerPage = '')
    {
        $where = ($notify == 2) ? 'WHERE active = 1' : '';
        $limitStmt = $this->_buildLimitStmt($limitStart, $resultsPerPage);
        return $this->db->fetchAll('SELECT u.id AS user_id_real, u.nickname AS user_name, u.website AS user_website, u.mail AS user_mail, g.* FROM ' . $this->prefix . static::TABLE_NAME . ' AS g LEFT JOIN ' . $this->prefix . \ACP3\Modules\Users\Model::TABLE_NAME . ' AS u ON(u.id = g.user_id) ' . $where . ' ORDER BY date DESC' . $limitStmt);
    }

    public function getAllInAcp()
    {
        return $this->db->fetchAll('SELECT * FROM ' . $this->prefix . static::TABLE_NAME . ' ORDER BY date DESC, id DESC');
    }

    public function validateCreate(array $formData, $newsletterAccess)
    {
        $this->validateFormKey();

        $errors = array();

        // Flood Sperre
        $flood = $this->getLastDateFromIp($_SERVER['REMOTE_ADDR']);
        $floodTime = !empty($flood) ? $this->date->timestamp($flood, true) + 30 : 0;
        $time = $this->date->timestamp('now', true);

        if ($floodTime > $time)
            $errors[] = sprintf($this->lang->t('system', 'flood_no_entry_possible'), $floodTime - $time);
        if (empty($formData['name']))
            $errors['name'] = $this->lang->t('system', 'name_to_short');
        if (!empty($formData['mail']) && Core\Validate::email($formData['mail']) === false)
            $errors['mail'] = $this->lang->t('system', 'wrong_email_format');
        if (strlen($formData['message']) < 3)
            $errors['message'] = $this->lang->t('system', 'message_to_short');
        if (Core\Modules::hasPermission('frontend/captcha/index/image') === true && $this->auth->isUser() === false && Core\Validate::captcha($formData['captcha']) === false)
            $errors['captcha'] = $this->lang->t('captcha', 'invalid_captcha_entered');
        if ($newsletterAccess === true && isset($formData['subscribe_newsletter']) && $formData['subscribe_newsletter'] == 1) {
            $newsletterModel = new \ACP3\Modules\Newsletter\Model($this->db, $this->lang, $this->auth);
            if (Core\Validate::email($formData['mail']) === false)
                $errors['mail'] = $this->lang->t('guestbook', 'type_in_email_address_to_subscribe_to_newsletter');
            if (Core\Validate::email($formData['mail']) === true && $newsletterModel->accountExists($formData['mail']) === true)
                $errors[] = $this->lang->t('newsletter', 'account_exists');
        }

        if (!empty($errors)) {
            throw new Core\Exceptions\ValidationFailed(Core\Functions::errorBox($errors));
        }
    }

    public function validateEdit(array $formData, array $settings)
    {
        $this->validateFormKey();

        $errors = array();
        if (empty($formData['name'])) {
            $errors['name'] = $this->lang->t('system', 'name_to_short');
        }
        if (strlen($formData['message']) < 3) {
            $errors['message'] = $this->lang->t('system', 'message_to_short');
        }
        if ($settings['notify'] == 2 && (!isset($formData['active']) || ($formData['active'] != 0 && $formData['active'] != 1))) {
            $errors['notify'] = $this->lang->t('guestbook', 'select_activate');
        }

        if (!empty($errors)) {
            throw new Core\Exceptions\ValidationFailed(Core\Functions::errorBox($errors));
        }
    }

    public function validateSettings(array $formData)
    {
        $this->validateFormKey();

        $errors = array();
        if (empty($formData['dateformat']) || ($formData['dateformat'] !== 'long' && $formData['dateformat'] !== 'short')) {
            $errors['dateformat'] = $this->lang->t('system', 'select_date_format');
        }
        if (!isset($formData['notify']) || ($formData['notify'] != 0 && $formData['notify'] != 1 && $formData['notify'] != 2)) {
            $errors['notify'] = $this->lang->t('guestbook', 'select_notification_type');
        }
        if ($formData['notify'] != 0 && Core\Validate::email($formData['notify_email']) === false) {
            $errors['notify-email'] = $this->lang->t('system', 'wrong_email_format');
        }
        if (!isset($formData['overlay']) || $formData['overlay'] != 1 && $formData['overlay'] != 0) {
            $errors[] = $this->lang->t('guestbook', 'select_use_overlay');
        }
        if (Core\Modules::isActive('emoticons') === true && (!isset($formData['emoticons']) || ($formData['emoticons'] != 0 && $formData['emoticons'] != 1))) {
            $errors[] = $this->lang->t('guestbook', 'select_emoticons');
        }
        if (Core\Modules::isActive('newsletter') === true && (!isset($formData['newsletter_integration']) || ($formData['newsletter_integration'] != 0 && $formData['newsletter_integration'] != 1))) {
            $errors[] = $this->lang->t('guestbook', 'select_newsletter_integration');
        }

        if (!empty($errors)) {
            throw new Core\Exceptions\ValidationFailed(Core\Functions::errorBox($errors));
        }
    }

}
