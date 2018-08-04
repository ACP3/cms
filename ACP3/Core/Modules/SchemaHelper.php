<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Core\Modules;

use ACP3\Core;
use Psr\Log\LoggerInterface;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;

class SchemaHelper
{
    use ContainerAwareTrait;

    /**
     * @var \ACP3\Core\Database\Connection
     */
    protected $db;
    /**
     * @var Core\Model\Repository\ModuleAwareRepositoryInterface
     */
    protected $systemModuleRepository;
    /**
     * @var Core\Model\Repository\SettingsAwareRepositoryInterface
     */
    protected $systemSettingsRepository;
    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * SchemaHelper constructor.
     *
     * @param LoggerInterface                                        $logger
     * @param Core\Database\Connection                               $db
     * @param Core\Model\Repository\ModuleAwareRepositoryInterface   $systemModuleRepository
     * @param Core\Model\Repository\SettingsAwareRepositoryInterface $systemSettingsRepository
     */
    public function __construct(
        LoggerInterface $logger,
        Core\Database\Connection $db,
        Core\Model\Repository\ModuleAwareRepositoryInterface $systemModuleRepository,
        Core\Model\Repository\SettingsAwareRepositoryInterface $systemSettingsRepository
    ) {
        $this->db = $db;
        $this->systemModuleRepository = $systemModuleRepository;
        $this->systemSettingsRepository = $systemSettingsRepository;
        $this->logger = $logger;
    }

    /**
     * @return \Symfony\Component\DependencyInjection\ContainerInterface
     */
    public function getContainer()
    {
        return $this->container;
    }

    /**
     * @return \ACP3\Core\Database\Connection
     */
    public function getDb()
    {
        return $this->db;
    }

    /**
     * @return Core\Model\Repository\ModuleAwareRepositoryInterface
     */
    public function getSystemModuleRepository()
    {
        return $this->systemModuleRepository;
    }

    /**
     * Executes all given SQL queries.
     *
     * @param array  $queries
     * @param string $moduleName
     *
     * @return bool
     *
     * @throws \Doctrine\DBAL\ConnectionException
     */
    public function executeSqlQueries(array $queries, string $moduleName = '')
    {
        if (\count($queries) > 0) {
            $search = ['{pre}', '{engine}', '{charset}'];
            $replace = [$this->db->getPrefix(), 'ENGINE=InnoDB', 'CHARACTER SET `utf8mb4` COLLATE `utf8mb4_unicode_ci`'];

            $this->db->getConnection()->beginTransaction();

            try {
                foreach ($queries as $query) {
                    if (\is_object($query) && ($query instanceof \Closure)) {
                        if ($query() === false) {
                            return false;
                        }
                    } elseif (!empty($query)) {
                        if (\strpos($query, '{moduleId}') !== false) {
                            $query = \str_replace('{moduleId}', $this->getModuleId($moduleName), $query);
                        }
                        $this->db->getConnection()->query(\str_ireplace($search, $replace, $query));
                    }
                }
                $this->db->getConnection()->commit();
            } catch (\Exception $e) {
                $this->db->getConnection()->rollBack();

                $this->logger->warning($e);

                return false;
            }
        }

        return true;
    }

    /**
     * Returns the module-ID.
     *
     * @param string $moduleName
     *
     * @return int
     */
    public function getModuleId(string $moduleName)
    {
        return $this->systemModuleRepository->getModuleId($moduleName) ?: 0;
    }

    /**
     * @param string $moduleName
     *
     * @return bool
     *
     * @throws \Doctrine\DBAL\DBALException
     */
    public function moduleIsInstalled(string $moduleName)
    {
        return $this->db->fetchColumn(
            "SELECT COUNT(*) FROM {$this->systemModuleRepository->getTableName()} WHERE `name` = ?",
            [$moduleName]
        ) == 1;
    }
}
