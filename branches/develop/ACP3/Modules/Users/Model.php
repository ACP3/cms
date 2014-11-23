<?php

namespace ACP3\Modules\Users;

use ACP3\Core;

/**
 * Class Model
 * @package ACP3\Modules\Users
 */
class Model extends Core\Model
{
    const TABLE_NAME = 'users';

    /**
     * @param $id
     * @return bool
     */
    public function resultExists($id)
    {
        return ((int)$this->db->getConnection()->fetchColumn('SELECT COUNT(*) FROM ' . $this->db->getPrefix() . static::TABLE_NAME . ' WHERE id = :id', array('id' => $id)) > 0);
    }

    /**
     * Überprüft, ob der übergebene Username bereits existiert
     *
     * @param string $nickname
     *  Der zu überprüfende Nickname
     * @param string $id
     *
     * @return boolean
     */
    public function resultExistsByUserName($nickname, $id = '')
    {
        if (!empty($id)) {
            return !empty($nickname) && $this->db->getConnection()->fetchColumn('SELECT COUNT(*) FROM ' . $this->db->getPrefix() . static::TABLE_NAME . ' WHERE id != ? AND nickname = ?', array((int)$id, $nickname)) == 1 ? true : false;
        } else {
            return !empty($nickname) && $this->db->getConnection()->fetchColumn('SELECT COUNT(*) FROM ' . $this->db->getPrefix() . static::TABLE_NAME . ' WHERE nickname = ?', array($nickname)) == 1 ? true : false;
        }
    }

    /**
     * Überprüft, ob die übergebene E-Mail-Adresse bereits existiert
     *
     * @param string $mail
     *  Die zu überprüfende E-Mail-Adresse
     * @param string $id
     *
     * @return boolean
     */
    public function resultExistsByEmail($mail, $id = '')
    {
        if (!empty($id)) {
            return $this->db->getConnection()->fetchColumn('SELECT COUNT(*) FROM ' . $this->db->getPrefix() . static::TABLE_NAME . ' WHERE id != ? AND mail = ?', array((int)$id, $mail)) > 0 ? true : false;
        } else {
            return $this->db->getConnection()->fetchColumn('SELECT COUNT(*) FROM ' . $this->db->getPrefix() . static::TABLE_NAME . ' WHERE mail = ?', array($mail)) > 0 ? true : false;
        }
    }

    /**
     * @param $id
     * @return array
     */
    public function getOneById($id)
    {
        return $this->db->getConnection()->fetchAssoc('SELECT * FROM ' . $this->db->getPrefix() . static::TABLE_NAME . ' WHERE id = ?', array($id));
    }

    /**
     * @param $nickname
     * @return array
     */
    public function getOneByNickname($nickname)
    {
        return $this->db->getConnection()->fetchAssoc('SELECT * FROM ' . $this->db->getPrefix() . static::TABLE_NAME . ' WHERE nickname = ?', array($nickname));
    }

    /**
     * @param $nickname
     * @return array
     */
    public function getOneActiveUserByNickname($nickname)
    {
        return $this->db->getConnection()->fetchAssoc('SELECT * FROM ' . $this->db->getPrefix() . static::TABLE_NAME . ' WHERE nickname = ? AND login_errors < 3', array($nickname));
    }

    /**
     * @param $email
     * @return array
     */
    public function getOneByEmail($email)
    {
        return $this->db->getConnection()->fetchAssoc('SELECT * FROM ' . $this->db->getPrefix() . static::TABLE_NAME . ' WHERE mail = ?', array($email));
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
     * @return array
     */
    public function getAll($limitStart = '', $resultsPerPage = '')
    {
        $limitStmt = $this->_buildLimitStmt($limitStart, $resultsPerPage);
        return $this->db->getConnection()->fetchAll('SELECT * FROM ' . $this->db->getPrefix() . static::TABLE_NAME . ' ORDER BY nickname ASC, id ASC' . $limitStmt);
    }

    /**
     * @return array
     */
    public function getAllInAcp()
    {
        return $this->db->getConnection()->fetchAll('SELECT * FROM ' . $this->db->getPrefix() . static::TABLE_NAME . ' ORDER BY nickname ASC');
    }

}