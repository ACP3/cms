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

    public function countAllAccounts()
    {
        return $this->db->fetchColumn('SELECT COUNT(*) FROM ' . $this->prefix . static::TABLE_NAME_ACCOUNTS);
    }

    public function countAllActiveAccounts()
    {
        return $this->db->fetchColumn('SELECT COUNT(*) FROM ' . $this->prefix . static::TABLE_NAME_ACCOUNTS . ' WHERE hash = ""');
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

    public function getAllActiveAccounts()
    {
        return $this->db->fetchAll('SELECT * FROM ' . $this->prefix . static::TABLE_NAME_ACCOUNTS . ' WHERE hash = "" ORDER BY id DESC');
    }

}
