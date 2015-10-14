<?php

namespace ACP3\Modules\ACP3\Users\Model;

use ACP3\Core;

/**
 * Class Model
 * @package ACP3\Modules\ACP3\Users
 */
class UserRepository extends Core\Model
{
    const TABLE_NAME = 'users';

    /**
     * @param $id
     *
     * @return bool
     */
    public function resultExists($id)
    {
        return ((int)$this->db->fetchColumn('SELECT COUNT(*) FROM ' . $this->getTableName() . ' WHERE id = :id', ['id' => $id]) > 0);
    }

    /**
     * Überprüft, ob der übergebene Username bereits existiert
     *
     * @param string $nickname
     *  Der zu überprüfende Nickname
     * @param int    $id
     *
     * @return boolean
     */
    public function resultExistsByUserName($nickname, $id = 0)
    {
        if (!empty($id)) {
            return !empty($nickname) && $this->db->fetchColumn('SELECT COUNT(*) FROM ' . $this->getTableName() . ' WHERE id != ? AND nickname = ?', [(int)$id, $nickname]) == 1 ? true : false;
        }

        return !empty($nickname) && $this->db->fetchColumn('SELECT COUNT(*) FROM ' . $this->getTableName() . ' WHERE nickname = ?', [$nickname]) == 1 ? true : false;
    }

    /**
     * Überprüft, ob die übergebene E-Mail-Adresse bereits existiert
     *
     * @param string $mail
     *  Die zu überprüfende E-Mail-Adresse
     * @param int    $id
     *
     * @return boolean
     */
    public function resultExistsByEmail($mail, $id = 0)
    {
        if (!empty($id)) {
            return $this->db->fetchColumn('SELECT COUNT(*) FROM ' . $this->getTableName() . ' WHERE id != ? AND mail = ?', [(int)$id, $mail]) > 0 ? true : false;
        } else {
            return $this->db->fetchColumn('SELECT COUNT(*) FROM ' . $this->getTableName() . ' WHERE mail = ?', [$mail]) > 0 ? true : false;
        }
    }

    /**
     * @param $id
     *
     * @return array
     */
    public function getOneById($id)
    {
        return $this->db->fetchAssoc('SELECT * FROM ' . $this->getTableName() . ' WHERE id = ?', [$id]);
    }

    /**
     * @param $nickname
     *
     * @return array
     */
    public function getOneByNickname($nickname)
    {
        return $this->db->fetchAssoc('SELECT * FROM ' . $this->getTableName() . ' WHERE nickname = ?', [$nickname]);
    }

    /**
     * @param $nickname
     *
     * @return array
     */
    public function getOneActiveUserByNickname($nickname)
    {
        return $this->db->fetchAssoc('SELECT * FROM ' . $this->getTableName() . ' WHERE nickname = ? AND login_errors < 3', [$nickname]);
    }

    /**
     * @param $email
     *
     * @return array
     */
    public function getOneByEmail($email)
    {
        return $this->db->fetchAssoc('SELECT * FROM ' . $this->getTableName() . ' WHERE mail = ?', [$email]);
    }

    /**
     * @return int
     */
    public function countAll()
    {
        return count($this->getAll());
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
        return $this->db->fetchAll('SELECT * FROM ' . $this->getTableName() . ' ORDER BY nickname ASC, id ASC' . $limitStmt);
    }

    /**
     * @return array
     */
    public function getAllInAcp()
    {
        return $this->db->fetchAll('SELECT * FROM ' . $this->getTableName() . ' ORDER BY nickname ASC');
    }
}
