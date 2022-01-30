<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Core\Modules;

use ACP3\Core;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;
use Symfony\Component\DependencyInjection\ContainerInterface;

class SchemaHelper
{
    use ContainerAwareTrait;

    /**
     * @var \ACP3\Core\Database\Connection
     */
    private $db;
    /**
     * @var Core\Model\Repository\ModuleAwareRepositoryInterface
     */
    private $systemModuleRepository;

    public function __construct(
        Core\Database\Connection $db,
        Core\Model\Repository\ModuleAwareRepositoryInterface $systemModuleRepository
    ) {
        $this->db = $db;
        $this->systemModuleRepository = $systemModuleRepository;
    }

    public function getContainer(): ContainerInterface
    {
        return $this->container;
    }

    /**
     * @return \ACP3\Core\Database\Connection
     */
    public function getDb(): Core\Database\Connection
    {
        return $this->db;
    }

    public function getSystemModuleRepository(): Core\Model\Repository\ModuleAwareRepositoryInterface
    {
        return $this->systemModuleRepository;
    }

    /**
     * Executes all given SQL queries.
     *
     * @throws \Doctrine\DBAL\ConnectionException
     * @throws \ACP3\Core\Modules\Exception\ModuleMigrationException
     * @throws \Doctrine\DBAL\Exception
     */
    public function executeSqlQueries(array $queries, string $moduleName = ''): void
    {
        if (\count($queries) === 0) {
            return;
        }

        $search = ['{pre}', '{engine}', '{charset}'];
        $replace = [$this->db->getPrefix(), 'ENGINE=InnoDB', 'CHARACTER SET `utf8mb4` COLLATE `utf8mb4_unicode_ci`'];

        if ($this->systemModuleRepository->coreTablesExist()) {
            $search[] = '{moduleId}';
            $replace[] = $this->getModuleId($moduleName);
        }

        try {
            foreach ($queries as $query) {
                if (\is_callable($query)) {
                    if ($query() === false) {
                        throw new Core\Modules\Exception\ModuleMigrationException(sprintf('An error occurred while executing a migration inside a closure for module "%s"', $moduleName));
                    }
                } elseif (!empty($query)) {
                    $this->db->getConnection()->query(str_ireplace($search, $replace, $query));
                }
            }
        } catch (\Exception $e) {
            throw new Core\Modules\Exception\ModuleMigrationException(sprintf('An error occurred while executing a migration for module "%s"', $moduleName), 0, $e);
        }
    }

    /**
     * Returns the module-ID.
     */
    public function getModuleId(string $moduleName): int
    {
        return $this->systemModuleRepository->getModuleId($moduleName) ?: 0;
    }

    public function moduleIsInstalled(string $moduleName): bool
    {
        return $this->systemModuleRepository->moduleExists($moduleName);
    }
}
