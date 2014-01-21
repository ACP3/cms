<?php

namespace ACP3\Modules\Polls;

use ACP3\Core;

/**
 * Description of Model
 *
 * @author Tino Goratsch
 */
class Model extends Core\Model
{

    const TABLE_NAME = 'polls';
    const TABLE_NAME_ANSWERS = 'poll_answers';
    const TABLE_NAME_VOTES = 'poll_votes';

    /**
     * @var \ACP3\Core\Lang
     */
    protected $lang;

    public function __construct(\Doctrine\DBAL\Connection $db, Core\Lang $lang)
    {
        parent::__construct($db);

        $this->lang = $lang;
    }

    public function pollExists($pollId, $time = '', $multiple = false)
    {
        $where = !empty($time) ? ' AND (start = end AND start <= :time OR start != end AND :time BETWEEN start AND end)' : '';
        $multiple = ($multiple === true) ? ' AND multiple = :multiple' : '';
        return $this->db->fetchColumn('SELECT COUNT(*) FROM ' . $this->prefix . static::TABLE_NAME . ' WHERE id = :id' . $where . $multiple, array('id' => $pollId, 'time' => $time, 'multiple' => 1)) > 0 ? true : false;
    }

    public function getOneById($pollId)
    {
        return $this->db->fetchAssoc('SELECT * FROM ' . $this->prefix . static::TABLE_NAME . ' WHERE id = :id', array('id' => $pollId));
    }

    public function getOneByIdWithTotalVotes($pollId)
    {
        return $this->db->fetchAssoc('SELECT p.*, COUNT(pv.poll_id) AS total_votes FROM ' . $this->prefix . static::TABLE_NAME . ' AS p LEFT JOIN ' . $this->prefix . static::TABLE_NAME_VOTES . ' AS pv ON(p.id = pv.poll_id) WHERE p.id = ?', array($pollId));
    }

    public function getAnswersById($id)
    {
        return $this->db->fetchAll('SELECT * FROM ' . $this->prefix . static::TABLE_NAME_ANSWERS . ' WHERE poll_id = ? ORDER BY id ASC', array($id));
    }

    public function countAll($status = '')
    {
        return $this->getAll($status);
    }

    public function getVotesByUserId($pollId, $userId, $ipAddress)
    {
        return $this->db->fetchColumn('SELECT COUNT(*) FROM ' . $this->prefix . static::TABLE_NAME_VOTES . ' WHERE poll_id = ? AND (user_id = ? OR ip = ?)', array($pollId, $userId, $ipAddress));
    }

    public function getVotesByIpAddress($pollId, $ipAddress)
    {
        return $this->db->fetchColumn('SELECT COUNT(*) FROM ' . $this->prefix . static::TABLE_NAME_VOTES . ' WHERE poll_id = ? AND ip = ?', array($pollId, $ipAddress));
    }

    public function getAnswersByPollId($pollId)
    {
        return $this->db->fetchAll('SELECT pa.id, pa.text, COUNT(pv.answer_id) AS votes FROM ' . $this->prefix . static::TABLE_NAME_ANSWERS . ' AS pa LEFT JOIN ' . $this->prefix . static::TABLE_NAME_VOTES . ' AS pv ON(pa.id = pv.answer_id) WHERE pa.poll_id = ? GROUP BY pa.id ORDER BY pa.id ASC', array($pollId));
    }

    public function getLatestPoll($time)
    {
        $period = 'p.start = p.end AND p.start <= :time OR p.start != p.end AND :time BETWEEN p.start AND p.end';
        return $this->db->fetchAssoc('SELECT p.id, p.title, p.multiple, COUNT(pv.poll_id) AS total_votes FROM ' . $this->prefix . static::TABLE_NAME . ' AS p LEFT JOIN ' . $this->prefix . static::TABLE_NAME_VOTES . ' AS pv ON(p.id = pv.poll_id) WHERE ' . $period . ' GROUP BY p.id ORDER BY p.start DESC LIMIT 1', array('time' => $time));
    }

    public function getAll($time = '', $limitStart = '', $resultsPerPage = '')
    {
        $where = empty($time) === false ? ' WHERE p.start <= :time' : '';
        $limitStmt = $this->_buildLimitStmt($limitStart, $resultsPerPage);
        return $this->db->fetchAll('SELECT p.id, p.start, p.end, p.title, COUNT(pv.poll_id) AS votes FROM ' . $this->prefix . static::TABLE_NAME . ' AS p LEFT JOIN ' . $this->prefix . static::TABLE_NAME_VOTES . ' AS pv ON(p.id = pv.poll_id)' . $where . ' GROUP BY p.id ORDER BY p.start DESC, p.end DESC, p.id DESC' . $limitStmt, array('time' => $time));
    }

    public function getAllInAcp()
    {
        return $this->db->fetchAll('SELECT * FROM ' . $this->prefix . static::TABLE_NAME . ' ORDER BY start DESC, end DESC, id DESC');
    }

    public function validateCreate(array $formData)
    {
        $this->validateFormKey($this->lang);

        $errors = array();
        if (Core\Validate::date($formData['start'], $formData['end']) === false) {
            $errors[] = $this->lang->t('system', 'select_date');
        }
        if (empty($formData['title'])) {
            $errors['title'] = $this->lang->t('polls', 'type_in_question');
        }
        $i = 0;
        foreach ($formData['answers'] as $row) {
            if (!empty($row)) {
                ++$i;
            }
        }
        if ($i <= 1) {
            $errors[] = $this->lang->t('polls', 'type_in_answer');
        }

        if (!empty($errors)) {
            throw new Core\Exceptions\ValidationFailed(Core\Functions::errorBox($errors));
        }
    }

    public function validateEdit(array $formData)
    {
        $this->validateFormKey($this->lang);

        $errors = array();
        if (Core\Validate::date($_POST['start'], $_POST['end']) === false) {
            $errors[] = $this->lang->t('system', 'select_date');
        }
        if (empty($_POST['title'])) {
            $errors['title'] = $this->lang->t('polls', 'type_in_question');
        }
        $markedAnswers = 0;
        $allAnswersEmpty = true;
        foreach ($_POST['answers'] as $row) {
            if (!empty($row['value'])) {
                $allAnswersEmpty = false;
            }
            if (isset($row['delete'])) {
                ++$markedAnswers;
            }
        }
        if ($allAnswersEmpty === true) {
            $errors[] = $this->lang->t('polls', 'type_in_answer');
        }
        if (count($_POST['answers']) - $markedAnswers < 2) {
            $errors[] = $this->lang->t('polls', 'can_not_delete_all_answers');
        }

        if (!empty($errors)) {
            throw new Core\Exceptions\ValidationFailed(Core\Functions::errorBox($errors));
        }
    }
}
