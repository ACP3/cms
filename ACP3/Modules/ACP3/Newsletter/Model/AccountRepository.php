<?php
namespace ACP3\Modules\ACP3\Newsletter\Model;

use ACP3\Core\Model\AbstractRepository;
use ACP3\Modules\ACP3\Newsletter\Helper\AccountStatus;

/**
 * Class AccountRepository
 * @package ACP3\Modules\ACP3\Newsletter\Model
 */
class AccountRepository extends AbstractRepository
{
    const TABLE_NAME = 'newsletter_accounts';

    /**
     * @param string $emailAddress
     * @param string $hash
     *
     * @return bool
     */
    public function accountExists($emailAddress, $hash = '')
    {
        $where = empty($hash) === false ? ' AND `hash` = :hash' : '';
        return $this->db->fetchColumn(
            "SELECT COUNT(*) FROM {$this->getTableName()} WHERE `mail` = :mail" . $where,
            ['mail' => $emailAddress, 'hash' => $hash]
        ) > 0;
    }

    /**
     * @param string $hash
     *
     * @return bool
     */
    public function accountExistsByHash($hash)
    {
        return $this->db->fetchColumn(
            "SELECT COUNT(*) FROM {$this->getTableName()} WHERE `hash` = :hash",
            ['hash' => $hash]
        ) > 0;
    }

    /**
     * @param string $email
     *
     * @return array
     */
    public function getOneByEmail($email)
    {
        return $this->db->fetchAssoc(
            "SELECT * FROM {$this->getTableName()} WHERE `mail` = :mail",
            ['mail' => $email]
        );
    }

    /**
     * @param string $hash
     *
     * @return array
     */
    public function getOneByHash($hash)
    {
        return $this->db->fetchAssoc(
            "SELECT * FROM {$this->getTableName()} WHERE `hash` = :hash",
            ['hash' => $hash]
        );
    }

    /**
     * @return mixed
     */
    public function countAllAccounts()
    {
        return $this->db->fetchColumn(
            "SELECT COUNT(*) FROM {$this->getTableName()} WHERE `status` != :status",
            ['status' => AccountStatus::ACCOUNT_STATUS_DISABLED]
        );
    }

    /**
     * @return mixed
     */
    public function countAllActiveAccounts()
    {
        return $this->db->fetchColumn(
            "SELECT COUNT(*) FROM {$this->getTableName()} WHERE `status` = :status",
            ['status' => AccountStatus::ACCOUNT_STATUS_CONFIRMED]
        );
    }

    /**
     * @return array
     */
    public function getAllAccounts()
    {
        return $this->db->fetchAll(
            "SELECT * FROM {$this->getTableName()} WHERE `status` != :status ORDER BY `id` DESC",
            ['status' => AccountStatus::ACCOUNT_STATUS_DISABLED]
        );
    }

    /**
     * @return array
     */
    public function getAllActiveAccounts()
    {
        return $this->db->fetchAll(
            "SELECT * FROM {$this->getTableName()} WHERE `status` = :status ORDER BY `id` DESC",
            ['status' => AccountStatus::ACCOUNT_STATUS_CONFIRMED]
        );
    }
}
