<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Users\Model\Repository;

use ACP3\Core;

class UserRepository extends Core\Model\Repository\AbstractRepository
{
    const TABLE_NAME = 'users';

    /**
     * @param int $userId
     *
     * @return bool
     *
     * @throws \Doctrine\DBAL\DBALException
     */
    public function resultExists(int $userId)
    {
        $query = "SELECT COUNT(*) FROM {$this->getTableName()} WHERE id = :id";

        return (int) $this->db->fetchColumn($query, ['id' => $userId]) > 0;
    }

    /**
     * Überprüft, ob der übergebene Username bereits existiert.
     *
     * @param string   $nickname
     * @param int|null $userId
     *
     * @return bool
     *
     * @throws \Doctrine\DBAL\DBALException
     */
    public function resultExistsByUserName(string $nickname, ?int $userId = null)
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
     * @param string   $mail
     * @param int|null $userId
     *
     * @return bool
     *
     * @throws \Doctrine\DBAL\DBALException
     */
    public function resultExistsByEmail(string $mail, ?int $userId = null)
    {
        if (!empty($userId)) {
            $query = 'SELECT COUNT(*) FROM ' . $this->getTableName() . ' WHERE id != ? AND mail = ?';

            return $this->db->fetchColumn($query, [$userId, $mail]) > 0;
        }

        return $this->db->fetchColumn(
                'SELECT COUNT(*) FROM ' . $this->getTableName() . ' WHERE mail = ?',
                [$mail]
            ) > 0;
    }

    /**
     * @param string $nickname
     *
     * @return array
     *
     * @throws \Doctrine\DBAL\DBALException
     */
    public function getOneByNickname(string $nickname)
    {
        return $this->db->fetchAssoc('SELECT * FROM ' . $this->getTableName() . ' WHERE nickname = ?', [$nickname]);
    }

    /**
     * @param string $nickname
     *
     * @return array
     *
     * @throws \Doctrine\DBAL\DBALException
     */
    public function getOneActiveUserByNickname(string $nickname)
    {
        return $this->db->fetchAssoc(
            'SELECT * FROM ' . $this->getTableName() . ' WHERE nickname = ? AND login_errors < 3',
            [$nickname]
        );
    }

    /**
     * @param string $email
     *
     * @return array
     *
     * @throws \Doctrine\DBAL\DBALException
     */
    public function getOneByEmail(string $email)
    {
        return $this->db->fetchAssoc(
            'SELECT * FROM ' . $this->getTableName() . ' WHERE mail = ?',
            [$email]
        );
    }

    /**
     * @return bool|string
     *
     * @throws \Doctrine\DBAL\DBALException
     */
    public function countAll()
    {
        return $this->db->fetchColumn("SELECT COUNT(*) FROM {$this->getTableName()}");
    }

    /**
     * @param int|null $limitStart
     * @param int|null $resultsPerPage
     *
     * @return array
     *
     * @throws \Doctrine\DBAL\DBALException
     */
    public function getAll(?int $limitStart = null, ?int $resultsPerPage = null)
    {
        $limitStmt = $this->buildLimitStmt($limitStart, $resultsPerPage);

        return $this->db->fetchAll(
            "SELECT * FROM {$this->getTableName()} ORDER BY `nickname` ASC, `id` ASC {$limitStmt}"
        );
    }
}
