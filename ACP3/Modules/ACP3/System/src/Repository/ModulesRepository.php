<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\System\Repository;

use ACP3\Core\Repository\AbstractRepository;
use ACP3\Core\Repository\ModuleAwareRepositoryInterface;
use Doctrine\DBAL\ArrayParameterType;

class ModulesRepository extends AbstractRepository implements ModuleAwareRepositoryInterface
{
    public const TABLE_NAME = 'modules';

    /**
     * @throws \Doctrine\DBAL\Exception
     */
    public function getModuleId(string $moduleName): int
    {
        return (int) $this->db->fetchColumn(
            'SELECT `id` FROM ' . $this->getTableName() . ' WHERE `name` = ?',
            [$moduleName]
        );
    }

    /**
     * @throws \Doctrine\DBAL\Exception
     */
    public function getModuleSchemaVersion(string $moduleName): int
    {
        return (int) $this->db->fetchColumn(
            'SELECT `version` FROM ' . $this->getTableName() . ' WHERE `name` = ?',
            [$moduleName]
        );
    }

    /**
     * @throws \Doctrine\DBAL\Exception
     */
    public function coreTablesExist(): bool
    {
        return ((int) $this->db->fetchColumn(
            'SELECT COUNT(*) FROM information_schema.tables WHERE table_schema = :dbName AND table_name IN (:requiredTables)',
            [
                'dbName' => $this->db->getDatabase(),
                'requiredTables' => [$this->getTableName(), $this->getTableName(SettingsRepository::TABLE_NAME)],
            ],
            [
                'requiredTables' => ArrayParameterType::STRING,
            ]
        )) === 2;
    }

    /**
     * @throws \Doctrine\DBAL\Exception
     */
    public function moduleExists(string $moduleName): bool
    {
        return $this->db->fetchColumn(
            'SELECT COUNT(*) FROM ' . $this->getTableName() . ' WHERE `name` = ?',
            [$moduleName]
        ) > 0;
    }

    /**
     * @throws \Doctrine\DBAL\Exception
     */
    public function getInfoByModuleName(string $moduleName): array
    {
        return $this->db->fetchAssoc(
            'SELECT `id`, `version` FROM ' . $this->getTableName() . ' WHERE `name` = ?',
            [$moduleName]
        );
    }

    /**
     * @throws \Doctrine\DBAL\Exception
     */
    public function getInfoByModuleNameList(array $moduleNames): array
    {
        $results = $this->db->fetchAll(
            "SELECT `id`, `name` FROM {$this->getTableName()} WHERE `name` IN(:moduleNames)",
            ['moduleNames' => $moduleNames],
            ['moduleNames' => ArrayParameterType::STRING]
        );

        $map = [];
        foreach ($results as $row) {
            $map[(string) $row['name']] = [
                'id' => (int) $row['id'],
            ];
        }

        return $map;
    }

    /**
     * @throws \Doctrine\DBAL\Exception
     */
    public function getModuleNameById(int $moduleId): string
    {
        return $this->db->fetchColumn(
            'SELECT `name` FROM ' . $this->getTableName() . ' WHERE `id` = ?',
            [$moduleId]
        );
    }
}
