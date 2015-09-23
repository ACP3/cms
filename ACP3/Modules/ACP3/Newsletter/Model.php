<?php

namespace ACP3\Modules\ACP3\Newsletter;

use ACP3\Core;
use ACP3\Modules\ACP3\Newsletter\Helper\Subscribe;

/**
 * Class Model
 * @package ACP3\Modules\ACP3\Newsletter
 */
class Model extends Core\Model
{
    const TABLE_NAME = 'newsletters';
    const TABLE_NAME_ACCOUNTS = 'newsletter_accounts';
    const TABLE_NAME_ACCOUNT_HISTORY = 'newsletter_account_history';

    /**
     * @param        $id
     * @param string $status
     *
     * @return bool
     */
    public function newsletterExists($id, $status = '')
    {
        $where = empty($status) === false ? ' AND status = :status' : '';
        return ((int)$this->db->fetchAssoc("SELECT COUNT(*) FROM {$this->getTableName()} WHERE `id` = :id" . $where, ['id' => $id, 'status' => $status]) > 0);
    }

    /**
     * @param string $emailAddress
     * @param string $hash
     *
     * @return bool
     */
    public function accountExists($emailAddress, $hash = '')
    {
        $where = empty($hash) === false ? ' AND hash = :hash' : '';
        return $this->db->fetchColumn("SELECT COUNT(*) FROM {$this->getTableName(static::TABLE_NAME_ACCOUNTS)} WHERE `mail` = :mail" . $where, ['mail' => $emailAddress, 'hash' => $hash]) > 0;
    }

    /**
     * @param string $hash
     *
     * @return bool
     */
    public function accountExistsByHash($hash)
    {
        return $this->db->fetchColumn("SELECT COUNT(*) FROM {$this->getTableName(static::TABLE_NAME_ACCOUNTS)} WHERE `hash` = :hash", ['hash' => $hash]) > 0;
    }

    /**
     * @param int    $id
     * @param string $status
     *
     * @return array
     */
    public function getOneById($id, $status = '')
    {
        $where = empty($status) === false ? ' AND status = :status' : '';
        return $this->db->fetchAssoc("SELECT * FROM {$this->getTableName()} WHERE id = :id {$where}", ['id' => $id, 'status' => $status]);
    }

    /**
     * @param string $email
     *
     * @return array
     */
    public function getOneByEmail($email)
    {
        return $this->db->fetchAssoc("SELECT * FROM {$this->getTableName()} WHERE mail = :mail", ['mail' => $email]);
    }

    /**
     * @param string $status
     *
     * @return mixed
     */
    public function countAll($status = '')
    {
        $where = empty($time) === false ? ' WHERE status = :status' : '';
        return $this->db->fetchColumn("SELECT COUNT(*) FROM {$this->getTableName()}{$where}", ['status' => $status]);
    }

    /**
     * @return mixed
     */
    public function countAllAccounts()
    {
        return $this->db->fetchColumn('SELECT COUNT(*) FROM ' . $this->getTableName(static::TABLE_NAME_ACCOUNTS));
    }

    /**
     * @return mixed
     */
    public function countAllActiveAccounts()
    {
        return $this->db->fetchColumn('SELECT COUNT(*) FROM ' . $this->getTableName(static::TABLE_NAME_ACCOUNTS) . ' WHERE `status` = ' . Subscribe::ACCOUNT_STATUS_CONFIRMED);
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
        $limitStmt = $this->buildLimitStmt($limitStart, $resultsPerPage);
        return $this->db->fetchAll("SELECT * FROM {$this->getTableName()}{$where} ORDER BY `date` DESC {$limitStmt}", ['status' => $status]);
    }

    /**
     * @return array
     */
    public function getAllInAcp()
    {
        return $this->db->fetchAll("SELECT * FROM {$this->getTableName()} ORDER BY `date` DESC");
    }

    /**
     * @return array
     */
    public function getAllAccounts()
    {
        return $this->db->fetchAll('SELECT * FROM ' . $this->getTableName(static::TABLE_NAME_ACCOUNTS) . ' ORDER BY `id` DESC');
    }

    /**
     * @return array
     */
    public function getAllActiveAccounts()
    {
        return $this->db->fetchAll('SELECT * FROM ' . $this->getTableName(static::TABLE_NAME_ACCOUNTS) . ' WHERE status = ' . Subscribe::ACCOUNT_STATUS_CONFIRMED . ' ORDER BY `id` DESC');
    }
}
