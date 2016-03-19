<?php

namespace ACP3\Modules\ACP3\Users\Model;

use ACP3\Core;

/**
 * Class Model
 * @package ACP3\Modules\ACP3\Users
 */
class UserRepository extends Core\Model\AbstractRepository
{
    const TABLE_NAME = 'users';

    /**
     * @param int $userId
     *
     * @return bool
     */
    public function resultExists($userId)
    {
        $query = 'SELECT COUNT(*) FROM ' . $this->getTableName() . ' WHERE id = :id';
        return ((int)$this->db->fetchColumn($query, ['id' => $userId]) > 0);
    }

    /**
     * Überprüft, ob der übergebene Username bereits existiert
     *
     * @param string $nickname
     * @param int    $userId
     *
     * @return boolean
     */
    public function resultExistsByUserName($nickname, $userId = 0)
    {
        if (!empty($userId)) {
            $query = 'SELECT COUNT(*) FROM ' . $this->getTableName() . ' WHERE id != ? AND nickname = ?';
            return !empty($nickname) && $this->db->fetchColumn($query, [(int)$userId, $nickname]) == 1;
        }

        $query = 'SELECT COUNT(*) FROM ' . $this->getTableName() . ' WHERE nickname = ?';
        return !empty($nickname) && $this->db->fetchColumn($query, [$nickname]) == 1;
    }

    /**
     * Überprüft, ob die übergebene E-Mail-Adresse bereits existiert
     *
     * @param string $mail
     * @param int    $userId
     *
     * @return boolean
     */
    public function resultExistsByEmail($mail, $userId = 0)
    {
        if (!empty($userId)) {
            $query = 'SELECT COUNT(*) FROM ' . $this->getTableName() . ' WHERE id != ? AND mail = ?';
            return $this->db->fetchColumn($query, [(int)$userId, $mail]) > 0;
        }
        return $this->db->fetchColumn('SELECT COUNT(*) FROM ' . $this->getTableName() . ' WHERE mail = ?', [$mail]) > 0;
    }

    /**
     * @param int $userId
     *
     * @return array
     */
    public function getOneById($userId)
    {
        return $this->db->fetchAssoc('SELECT * FROM ' . $this->getTableName() . ' WHERE id = ?', [$userId]);
    }

    /**
     * @param string $nickname
     *
     * @return array
     */
    public function getOneByNickname($nickname)
    {
        return $this->db->fetchAssoc('SELECT * FROM ' . $this->getTableName() . ' WHERE nickname = ?', [$nickname]);
    }

    /**
     * @param string $nickname
     *
     * @return array
     */
    public function getOneActiveUserByNickname($nickname)
    {
        return $this->db->fetchAssoc(
            'SELECT * FROM ' . $this->getTableName() . ' WHERE nickname = ? AND login_errors < 3',
            [$nickname]
        );
    }

    /**
     * @param string $email
     *
     * @return array
     */
    public function getOneByEmail($email)
    {
        return $this->db->fetchAssoc(
            'SELECT * FROM ' . $this->getTableName() . ' WHERE mail = ?',
            [$email]
        );
    }

    /**
     * @return int
     */
    public function countAll()
    {
        return $this->db->fetchColumn("SELECT COUNT(*) FROM {$this->getTableName()}");
    }

    /**
     * @param string $limitStart
     * @param string $resultsPerPage
     *
     * @return array
     */
    public function getAll($limitStart = '', $resultsPerPage = '')
    {
        $limitStmt = $this->buildLimitStmt($limitStart, $resultsPerPage);
        return $this->db->fetchAll(
            "SELECT * FROM {$this->getTableName()} ORDER BY `nickname` ASC, `id` ASC {$limitStmt}"
        );
    }
}
