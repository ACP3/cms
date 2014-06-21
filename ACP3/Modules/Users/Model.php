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

}
