<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Files\Repository;

use ACP3\Core;
use ACP3\Core\Database\Connection;
use ACP3\Modules\ACP3\Files\Installer\Schema;

class FilesRepository extends Core\Repository\AbstractRepository
{
    use Core\Repository\PublicationPeriodAwareTrait;

    public const TABLE_NAME = 'files';

    /**
     * @var Core\Settings\SettingsInterface
     */
    private $settings;

    public function __construct(Connection $db, Core\Settings\SettingsInterface $settings)
    {
        parent::__construct($db);

        $this->settings = $settings;
    }

    /**
     * @throws \Doctrine\DBAL\Exception
     */
    public function resultExists(int $fileId, string $time = ''): bool
    {
        $period = empty($time) === false ? ' AND ' . $this->getPublicationPeriod() . ' AND `active` = :active' : '';

        return (int) $this->db->fetchColumn(
                "SELECT COUNT(*) FROM {$this->getTableName()} WHERE `id` = :id" . $period,
                ['id' => $fileId, 'time' => $time, 'active' => 1]
            ) > 0;
    }

    /**
     * @throws \Doctrine\DBAL\Exception
     */
    public function getOneById(int $fileId): array
    {
        return $this->db->fetchAssoc(
            'SELECT n.*, c.title AS category_title FROM ' . $this->getTableName() . ' AS n LEFT JOIN ' . $this->getTableName(\ACP3\Modules\ACP3\Categories\Repository\CategoryRepository::TABLE_NAME) . ' AS c ON(n.category_id = c.id) WHERE n.id = ?',
            [$fileId]
        );
    }

    /**
     * @throws \Doctrine\DBAL\Exception
     */
    public function getFileById(int $fileId): string
    {
        return $this->db->fetchColumn("SELECT `file` FROM {$this->getTableName()} WHERE `id` = ?", [$fileId]);
    }

    /**
     * @throws \Doctrine\DBAL\Exception
     */
    public function countAll(string $time = '', ?int $categoryId = null): int
    {
        if ($categoryId !== null) {
            $results = $this->getAllByCategoryId($categoryId, $time);
        } else {
            $results = $this->getAll($time);
        }

        return \count($results);
    }

    /**
     * @throws \Doctrine\DBAL\Exception
     */
    public function getAllByCategoryId(int $categoryId, string $time = '', ?int $limitStart = null, ?int $resultsPerPage = null): array
    {
        $where = empty($time) === false ? ' AND ' . $this->getPublicationPeriod() . ' AND `active` = :active' : '';
        $limitStmt = $this->buildLimitStmt($limitStart, $resultsPerPage);

        return $this->db->fetchAll(
            "SELECT * FROM {$this->getTableName()} WHERE `category_id` = :categoryId {$where} ORDER BY {$this->getOrderBy()}{$limitStmt}",
            ['time' => $time, 'active' => 1, 'categoryId' => $categoryId]
        );
    }

    /**
     * @throws \Doctrine\DBAL\Exception
     */
    public function getAll(string $time = '', ?int $limitStart = null, ?int $resultsPerPage = null): array
    {
        $where = empty($time) === false ? ' WHERE ' . $this->getPublicationPeriod() . ' AND `active` = :active' : '';
        $limitStmt = $this->buildLimitStmt($limitStart, $resultsPerPage);

        return $this->db->fetchAll(
            "SELECT * FROM {$this->getTableName()}{$where} ORDER BY {$this->getOrderBy()}{$limitStmt}",
            ['time' => $time, 'active' => 1]
        );
    }

    /**
     * @throws \Doctrine\DBAL\Exception
     */
    public function getMaxSort(): int
    {
        return (int) $this->db->fetchColumn("SELECT MAX(`sort`) FROM {$this->getTableName()};");
    }

    private function getOrderBy(): string
    {
        $settings = $this->settings->getSettings(Schema::MODULE_NAME);

        $orderByMap = [
            'date' => '`start` DESC, `end` DESC, `id` DESC',
            'custom' => '`sort` ASC',
        ];

        if (isset($settings['order_by'], $orderByMap[$settings['order_by']])) {
            return $orderByMap[$settings['order_by']];
        }

        return $orderByMap['date'];
    }
}
