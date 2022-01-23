<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Newsletter\Repository;

use ACP3\Core\Repository\AbstractRepository;
use ACP3\Modules\ACP3\Newsletter\Helper\AccountStatus;

class AccountRepository extends AbstractRepository
{
    public const TABLE_NAME = 'newsletter_accounts';

    public function accountExists(string $emailAddress, string $hash = ''): bool
    {
        $where = empty($hash) === false ? ' AND `hash` = :hash' : '';

        return $this->db->fetchColumn(
                "SELECT COUNT(*) FROM {$this->getTableName()} WHERE `mail` = :mail" . $where,
                ['mail' => $emailAddress, 'hash' => $hash]
        ) > 0;
    }

    public function accountExistsByHash(string $hash): bool
    {
        return $this->db->fetchColumn(
                "SELECT COUNT(*) FROM {$this->getTableName()} WHERE `hash` = :hash",
                ['hash' => $hash]
        ) > 0;
    }

    /**
     * @return array<string, mixed>
     */
    public function getOneByEmail(string $email): array
    {
        return $this->db->fetchAssoc(
            "SELECT * FROM {$this->getTableName()} WHERE `mail` = :mail",
            ['mail' => $email]
        );
    }

    /**
     * @return array<string, mixed>
     */
    public function getOneByHash(string $hash): array
    {
        return $this->db->fetchAssoc(
            "SELECT * FROM {$this->getTableName()} WHERE `hash` = :hash",
            ['hash' => $hash]
        );
    }

    public function countAllAccounts(): int
    {
        return (int) $this->db->fetchColumn(
            "SELECT COUNT(*) FROM {$this->getTableName()} WHERE `status` != :status",
            ['status' => AccountStatus::ACCOUNT_STATUS_DISABLED]
        );
    }

    public function countAllActiveAccounts(): int
    {
        return (int) $this->db->fetchColumn(
            "SELECT COUNT(*) FROM {$this->getTableName()} WHERE `status` = :status",
            ['status' => AccountStatus::ACCOUNT_STATUS_CONFIRMED]
        );
    }

    /**
     * @return array<string, mixed>[]
     */
    public function getAllActiveAccounts(): array
    {
        return $this->db->fetchAll(
            "SELECT * FROM {$this->getTableName()} WHERE `status` = :status ORDER BY `id` DESC",
            ['status' => AccountStatus::ACCOUNT_STATUS_CONFIRMED]
        );
    }
}
