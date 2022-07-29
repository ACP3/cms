<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Articles\Repository;

use ACP3\Core;

class ArticleRepository extends Core\Repository\AbstractRepository
{
    use Core\Repository\PublicationPeriodAwareTrait;

    public const TABLE_NAME = 'articles';

    /**
     * @throws \Doctrine\DBAL\Exception
     */
    public function resultExists(int $articleId, string $time = ''): bool
    {
        $period = empty($time) === false ? ' AND ' . $this->getPublicationPeriod() . ' AND `active` = :active' : '';

        return $this->db->fetchColumn(
            "SELECT COUNT(*) FROM {$this->getTableName()} WHERE id = :id{$period}",
            ['id' => $articleId, 'time' => $time, 'active' => 1]
        ) > 0;
    }

    /**
     * @throws \Doctrine\DBAL\Exception
     */
    public function countAll(string $time = ''): int
    {
        $where = empty($time) === false ? ' WHERE ' . $this->getPublicationPeriod() . ' AND `active` = :active' : '';

        return (int) $this->db->fetchColumn(
            "SELECT COUNT(*) FROM {$this->getTableName()}{$where}",
            ['time' => $time, 'active' => 1]
        );
    }

    /**
     * @return array<array<string, mixed>>
     *
     * @throws \Doctrine\DBAL\Exception
     */
    public function getAll(string $time = '', ?int $limitStart = null, ?int $resultsPerPage = null): array
    {
        $where = empty($time) === false ? ' WHERE ' . $this->getPublicationPeriod() . ' AND `active` = :active' : '';
        $limitStmt = $this->buildLimitStmt($limitStart, $resultsPerPage);

        return $this->db->fetchAll(
            "SELECT * FROM {$this->getTableName()}{$where} ORDER BY `title` ASC{$limitStmt}",
            ['time' => $time, 'active' => 1]
        );
    }

    /**
     * @return array<array<string, mixed>>
     *
     * @throws \Doctrine\DBAL\Exception
     */
    public function getLatest(string $time = '', ?int $limitStart = null, ?int $resultsPerPage = null): array
    {
        $where = empty($time) === false ? ' WHERE ' . $this->getPublicationPeriod() . ' AND `active` = :active' : '';
        $limitStmt = $this->buildLimitStmt($limitStart, $resultsPerPage);

        return $this->db->fetchAll(
            "SELECT * FROM {$this->getTableName()}{$where} ORDER BY `start` DESC{$limitStmt}",
            ['time' => $time, 'active' => 1]
        );
    }
}
