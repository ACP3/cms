<?php

namespace ACP3\Modules\Comments;

use ACP3\Core;

/**
 * Description of Model
 *
 * @author goratsch
 */
class Model extends Core\Model
{

    const TABLE_NAME = 'comments';

    protected $auth;
    protected $date;

    public function __construct(\Doctrine\DBAL\Connection $db, Core\Lang $lang, Core\Auth $auth, Core\Date $date)
    {
        parent::__construct($db, $lang);

        $this->auth = $auth;
        $this->date = $date;
    }

    public function resultExists($id)
    {
        return $this->db->fetchColumn('SELECT COUNT(*) FROM ' . $this->prefix . static::TABLE_NAME . ' WHERE id = ?', array($id)) > 0 ? true : false;
    }

    public function resultsExist($moduleId)
    {
        return $this->db->fetchColumn('SELECT COUNT(*) FROM ' . $this->prefix . static::TABLE_NAME . ' WHERE module_id = ?', array($moduleId)) > 0 ? true : false;
    }

    public function getOneById($id)
    {
        return $this->db->fetchAssoc('SELECT c.*, m.name AS module FROM ' . $this->prefix . static::TABLE_NAME . ' AS c JOIN ' . $this->prefix . 'modules AS m ON(m.id = c.module_id) WHERE c.id = ?', array($id));
    }

    public function getLastDateFromIp($ipAddress)
    {
        return $this->db->fetchColumn('SELECT MAX(date) FROM ' . $this->prefix . static::TABLE_NAME . ' WHERE ip = ?', array($ipAddress));
    }

    public function getAllByModule($moduleId, $resultId, $limitStart = '', $resultsPerPage = '')
    {
        $limitStmt = $this->_buildLimitStmt($limitStart, $resultsPerPage);
        return $this->db->fetchAll('SELECT u.nickname AS user_name, c.name, c.user_id, c.date, c.message FROM ' . $this->prefix . static::TABLE_NAME . ' AS c JOIN ' . $this->prefix . 'modules AS m ON(m.id = c.module_id) LEFT JOIN (' . $this->prefix . 'users AS u) ON u.id = c.user_id WHERE m.name = ? AND c.entry_id = ? ORDER BY c.date ASC' . $limitStmt, array($moduleId, $resultId));
    }

    public function countAllByModule($moduleId, $resultId)
    {
        return $this->db->fetchColumn('SELECT COUNT(*) FROM ' . $this->prefix . static::TABLE_NAME . ' AS c JOIN ' . $this->prefix . 'modules AS m ON(m.id = c.module_id) WHERE m.name = ? AND c.entry_id = ?', array($moduleId, $resultId));
    }

    public function getAllByModuleInAcp($moduleId)
    {
        return $this->db->fetchAll('SELECT IF(c.name != "" AND c.user_id = 0,c.name,u.nickname) AS name, c.id, c.ip, c.user_id, c.date, c.message FROM ' . $this->prefix . static::TABLE_NAME . ' AS c LEFT JOIN ' . $this->prefix . 'users AS u ON u.id = c.user_id WHERE c.module_id = ? ORDER BY c.entry_id ASC, c.id ASC', array($moduleId));
    }

    public function getCommentsGroupedByModule()
    {
        return $this->db->fetchAll('SELECT c.module_id, m.name AS module, COUNT(c.module_id) AS `comments_count` FROM ' . $this->prefix . static::TABLE_NAME . ' AS c JOIN ' . $this->prefix . 'modules AS m ON(m.id = c.module_id) GROUP BY c.module_id ORDER BY m.name');
    }

    public function validateCreate(array $formData, $ip)
    {
        $this->validateFormKey();

        // Flood Sperre
        $flood = $this->getLastDateFromIp($ip);
        $floodTime = !empty($flood) ? $this->date->timestamp($flood, true) + 30 : 0;
        $time = $this->date->timestamp('now', true);

        $errors = array();
        if ($floodTime > $time) {
            $errors[] = sprintf($this->lang->t('system', 'flood_no_entry_possible'), $floodTime - $time);
        }
        if (empty($formData['name'])) {
            $errors['name'] = $this->lang->t('system', 'name_to_short');
        }
        if (strlen($formData['message']) < 3) {
            $errors['message'] = $this->lang->t('system', 'message_to_short');
        }
        if (Core\Modules::hasPermission('frontend/captcha/index/image') === true && $this->auth->isUser() === false && Core\Validate::captcha($formData['captcha']) === false) {
            $errors['captcha'] = $this->lang->t('captcha', 'invalid_captcha_entered');
        }

        if (!empty($errors)) {
            throw new Core\Exceptions\ValidationFailed(Core\Functions::errorBox($errors));
        }
    }

    public function validateEdit(array $formData)
    {
        $this->validateFormKey();

        $errors = array();
        if ((empty($comment['user_id']) || Core\Validate::isNumber($comment['user_id']) === false) && empty($formData['name'])) {
            $errors['name'] = $this->lang->t('system', 'name_to_short');
        }
        if (strlen($formData['message']) < 3) {
            $errors['message'] = $this->lang->t('system', 'message_to_short');
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
        if (Core\Modules::isActive('emoticons') === true && (!isset($formData['emoticons']) || ($formData['emoticons'] != 0 && $formData['emoticons'] != 1))) {
            $errors[] = $this->lang->t('comments', 'select_emoticons');
        }

        if (!empty($errors)) {
            throw new Core\Exceptions\ValidationFailed(Core\Functions::errorBox($errors));
        }
    }

}
