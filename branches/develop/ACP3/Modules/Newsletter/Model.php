<?php

namespace ACP3\Modules\Newsletter;

use ACP3\Core;

/**
 * Class Model
 * @package ACP3\Modules\Newsletter
 */
class Model extends Core\Model
{

    const TABLE_NAME = 'newsletters';
    const TABLE_NAME_ACCOUNTS = 'newsletter_accounts';

    /**
     * @param $id
     * @param string $status
     * @return bool
     */
    public function newsletterExists($id, $status = '')
    {
        $where = empty($status) === false ? ' AND status = :status' : '';
        return ((int)$this->db->getConnection()->fetchAssoc('SELECT COUNT(*) FROM ' . $this->db->getPrefix() . static::TABLE_NAME . ' WHERE id = :id' . $where, array('id' => $id, 'status' => $status)) > 0);
    }

    /**
     * @param $emailAddress
     * @param string $hash
     * @return bool
     */
    public function accountExists($emailAddress, $hash = '')
    {
        $where = empty($hash) === false ? ' AND hash = :hash' : '';
        return $this->db->getConnection()->fetchColumn('SELECT COUNT(*) FROM ' . $this->db->getPrefix() . static::TABLE_NAME_ACCOUNTS . ' WHERE mail = :mail' . $where, array('mail' => $emailAddress, 'hash' => $hash)) > 0;
    }

    /**
     * @param $id
     * @param string $status
     * @return array
     */
    public function getOneById($id, $status = '')
    {
        $where = empty($status) === false ? ' AND status = :status' : '';
        return $this->db->getConnection()->fetchAssoc('SELECT * FROM ' . $this->db->getPrefix() . static::TABLE_NAME . ' WHERE id = :id' . $where, array('id' => $id, 'status' => $status));
    }

    /**
     * @param string $status
     * @return mixed
     */
    public function countAll($status = '')
    {
        $where = empty($time) === false ? ' WHERE status = :status' : '';
        return $this->db->getConnection()->fetchColumn('SELECT COUNT(*) FROM ' . $this->db->getPrefix() . static::TABLE_NAME . $where, array('status' => $status));
    }

    /**
     * @return mixed
     */
    public function countAllAccounts()
    {
        return $this->db->getConnection()->fetchColumn('SELECT COUNT(*) FROM ' . $this->db->getPrefix() . static::TABLE_NAME_ACCOUNTS);
    }

    /**
     * @return mixed
     */
    public function countAllActiveAccounts()
    {
        return $this->db->getConnection()->fetchColumn('SELECT COUNT(*) FROM ' . $this->db->getPrefix() . static::TABLE_NAME_ACCOUNTS . ' WHERE hash = ""');
    }

    /**
     * @param string $status
     * @param string $limitStart
     * @param string $resultsPerPage
     * @return array
     */
    public function getAll($status = '', $limitStart = '', $resultsPerPage = '')
    {
        $where = empty($status) === false ? ' WHERE status = :status' : '';
        $limitStmt = $this->_buildLimitStmt($limitStart, $resultsPerPage);
        return $this->db->getConnection()->fetchAll('SELECT * FROM ' . $this->db->getPrefix() . static::TABLE_NAME . $where . ' ORDER BY date DESC' . $limitStmt, array('status' => $status));
    }

    /**
     * @return array
     */
    public function getAllInAcp()
    {
        return $this->db->getConnection()->fetchAll('SELECT * FROM ' . $this->db->getPrefix() . static::TABLE_NAME . ' ORDER BY date DESC');
    }

    /**
     * @return array
     */
    public function getAllAccounts()
    {
        return $this->db->getConnection()->fetchAll('SELECT * FROM ' . $this->db->getPrefix() . static::TABLE_NAME_ACCOUNTS . ' ORDER BY id DESC');
    }

    /**
     * @return array
     */
    public function getAllActiveAccounts()
    {
        return $this->db->getConnection()->fetchAll('SELECT * FROM ' . $this->db->getPrefix() . static::TABLE_NAME_ACCOUNTS . ' WHERE hash = "" ORDER BY id DESC');
    }

}
