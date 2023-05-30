<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Users\Repository;

use ACP3\Core;

class UserRepository extends Core\Repository\AbstractRepository
{
    public const TABLE_NAME = 'users';

    /**
     * @throws \Doctrine\DBAL\Exception
     */
    public function resultExists(int $userId): bool
    {
        $query = "SELECT COUNT(*) FROM {$this->getTableName()} WHERE id = :id";

        return (int) $this->db->fetchColumn($query, ['id' => $userId]) > 0;
    }

    /**
     * Überprüft, ob der übergebene Username bereits existiert.
     *
     * @throws \Doctrine\DBAL\Exception
     */
    public function resultExistsByUserName(string $nickname, int $userId = null): bool
    {
        if (!empty($userId)) {
            $query = 'SELECT COUNT(*) FROM ' . $this->getTableName() . ' WHERE id != ? AND nickname = ?';

            return !empty($nickname) && $this->db->fetchColumn($query, [$userId, $nickname]) == 1;
        }

        $query = 'SELECT COUNT(*) FROM ' . $this->getTableName() . ' WHERE nickname = ?';

        return !empty($nickname) && $this->db->fetchColumn($query, [$nickname]) == 1;
    }

    /**
     * Überprüft, ob die übergebene E-Mail-Adresse bereits existiert.
     *
     * @throws \Doctrine\DBAL\Exception
     */
    public function resultExistsByEmail(string $mail, int $userId = null): bool
    {
        if (!empty($userId)) {
            $query = 'SELECT COUNT(*) FROM ' . $this->getTableName() . ' WHERE id != ? AND mail = ?';

            return $this->db->fetchColumn($query, [$userId, $mail]) > 0;
        }

        return $this->db->fetchColumn('SELECT COUNT(*) FROM ' . $this->getTableName() . ' WHERE mail = ?', [$mail]) > 0;
    }

    /**
     * @return array<string, mixed>
     *
     * @throws \Doctrine\DBAL\Exception
     */
    public function getOneByNickname(string $nickname): array
    {
        return $this->db->fetchAssoc('SELECT * FROM ' . $this->getTableName() . ' WHERE nickname = ?', [$nickname]);
    }

    /**
     * @return array<string, mixed>
     *
     * @throws \Doctrine\DBAL\Exception
     */
    public function getOneActiveUserByNickname(string $nickname): array
    {
        return $this->db->fetchAssoc(
            'SELECT * FROM ' . $this->getTableName() . ' WHERE nickname = ? AND login_errors < 3',
            [$nickname]
        );
    }

    /**
     * @return array<string, mixed>
     *
     * @throws \Doctrine\DBAL\Exception
     */
    public function getOneByEmail(string $email): array
    {
        return $this->db->fetchAssoc(
            'SELECT * FROM ' . $this->getTableName() . ' WHERE mail = ?',
            [$email]
        );
    }

    /**
     * @throws \Doctrine\DBAL\Exception
     */
    public function countAll(): int
    {
        return (int) $this->db->fetchColumn("SELECT COUNT(*) FROM {$this->getTableName()}");
    }

    /**
     * @return array<string, mixed>[]
     *
     * @throws \Doctrine\DBAL\Exception
     */
    public function getAll(int $limitStart = null, int $resultsPerPage = null): array
    {
        $limitStmt = $this->buildLimitStmt($limitStart, $resultsPerPage);

        return $this->db->fetchAll(
            "SELECT * FROM {$this->getTableName()} ORDER BY `nickname` ASC, `id` ASC {$limitStmt}"
        );
    }
}
