<?php

namespace ACP3\Modules\ACP3\Newsletter;

use ACP3\Core;

/**
 * Class Model
 * @package ACP3\Modules\ACP3\Newsletter
 */
class Model extends Core\Model
{
    const TABLE_NAME = 'newsletters';
    const TABLE_NAME_ACCOUNTS = 'newsletter_accounts';

    /**
     * @param        $id
     * @param string $status
     *
     * @return bool
     */
    public function newsletterExists($id, $status = '')
    {
        $where = empty($status) === false ? ' AND status = :status' : '';
        return ((int)$this->db->fetchAssoc('SELECT COUNT(*) FROM ' . $this->db->getPrefix() . static::TABLE_NAME . ' WHERE id = :id' . $where, ['id' => $id, 'status' => $status]) > 0);
    }

    /**
     * @param        $emailAddress
     * @param string $hash
     *
     * @return bool
     */
    public function accountExists($emailAddress, $hash = '')
    {
        $where = empty($hash) === false ? ' AND hash = :hash' : '';
        return $this->db->fetchColumn('SELECT COUNT(*) FROM ' . $this->db->getPrefix() . static::TABLE_NAME_ACCOUNTS . ' WHERE mail = :mail' . $where, ['mail' => $emailAddress, 'hash' => $hash]) > 0;
    }

    /**
     * @param        $id
     * @param string $status
     *
     * @return array
     */
    public function getOneById($id, $status = '')
    {
        $where = empty($status) === false ? ' AND status = :status' : '';
        return $this->db->fetchAssoc('SELECT * FROM ' . $this->db->getPrefix() . static::TABLE_NAME . ' WHERE id = :id' . $where, ['id' => $id, 'status' => $status]);
    }

    /**
     * @param string $status
     *
     * @return mixed
     */
    public function countAll($status = '')
    {
        $where = empty($time) === false ? ' WHERE status = :status' : '';
        return $this->db->fetchColumn('SELECT COUNT(*) FROM ' . $this->db->getPrefix() . static::TABLE_NAME . $where, ['status' => $status]);
    }

    /**
     * @return mixed
     */
    public function countAllAccounts()
    {
        return $this->db->fetchColumn('SELECT COUNT(*) FROM ' . $this->db->getPrefix() . static::TABLE_NAME_ACCOUNTS);
    }

    /**
     * @return mixed
     */
    public function countAllActiveAccounts()
    {
        return $this->db->fetchColumn('SELECT COUNT(*) FROM ' . $this->db->getPrefix() . static::TABLE_NAME_ACCOUNTS . ' WHERE HASH = ""');
    }

    /**
     * @param string $status
     * @param string $limitStart
     * @param string $resultsPerPage
     *
     * @return array
     */
    public function getAll($status = '', $limitStart = '', $resultsPerPage = '')
    {
        $where = empty($status) === false ? ' WHERE status = :status' : '';
        $limitStmt = $this->_buildLimitStmt($limitStart, $resultsPerPage);
        return $this->db->fetchAll('SELECT * FROM ' . $this->db->getPrefix() . static::TABLE_NAME . $where . ' ORDER BY DATE DESC' . $limitStmt, ['status' => $status]);
    }

    /**
     * @return array
     */
    public function getAllInAcp()
    {
        return $this->db->fetchAll('SELECT * FROM ' . $this->db->getPrefix() . static::TABLE_NAME . ' ORDER BY DATE DESC');
    }

    /**
     * @return array
     */
    public function getAllAccounts()
    {
        return $this->db->fetchAll('SELECT * FROM ' . $this->db->getPrefix() . static::TABLE_NAME_ACCOUNTS . ' ORDER BY id DESC');
    }

    /**
     * @return array
     */
    public function getAllActiveAccounts()
    {
        return $this->db->fetchAll('SELECT * FROM ' . $this->db->getPrefix() . static::TABLE_NAME_ACCOUNTS . ' WHERE HASH = "" ORDER BY id DESC');
    }
}
