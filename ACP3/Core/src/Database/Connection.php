<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Core\Database;

use ACP3\Core\Environment\ApplicationMode;
use Doctrine\DBAL;
use Doctrine\DBAL\Cache\QueryCacheProfile;
use Doctrine\DBAL\Result;
use Doctrine\DBAL\Types\Type;
use Psr\Log\LoggerInterface;

class Connection
{
    private ?DBAL\Connection $connection = null;

    public function __construct(private LoggerInterface $logger, private string $appMode, private array $connectionParams, private string $tablePrefix)
    {
    }

    /**
     * @throws \Doctrine\DBAL\Exception
     */
    public function getConnection(): DBAL\Connection
    {
        if ($this->connection === null) {
            $this->connection = $this->connect();
        }

        return $this->connection;
    }

    /**
     * @throws DBAL\Exception
     */
    public function beginTransaction(): void
    {
        $this->getConnection()->beginTransaction();
    }

    /**
     * @throws DBAL\Exception
     */
    public function commit(): void
    {
        $this->getConnection()->commit();
    }

    /**
     * @throws DBAL\Exception
     */
    public function rollback(): void
    {
        $this->getConnection()->rollBack();
    }

    /**
     * @throws DBAL\Exception
     */
    public function getWrappedConnection(): ?\PDO
    {
        $connection = $this->getConnection()->getWrappedConnection();

        if ($connection instanceof DBAL\Driver\PDO\Connection) {
            return $connection->getWrappedConnection();
        }

        return null;
    }

    public function getDatabase(): string
    {
        return $this->connectionParams['dbname'];
    }

    public function getPrefix(): string
    {
        return $this->tablePrefix;
    }

    public function getPrefixedTableName(string $tableName): string
    {
        return $this->tablePrefix . $tableName;
    }

    /**
     * @param array<int, int|string|Type|null>|array<string, int|string|Type|null> $types
     *
     * @throws \Doctrine\DBAL\Exception
     */
    public function fetchAll(
        string $statement,
        array $params = [],
        array $types = [],
        bool $cache = false,
        int $lifetime = 0,
        ?string $cacheKey = null
    ): array {
        $stmt = $this->executeQuery($statement, $params, $types, $cache, $lifetime, $cacheKey);
        $data = $stmt->fetchAllAssociative();
        $stmt->free();

        return $data;
    }

    /**
     * @param array<int, int|string|Type|null>|array<string, int|string|Type|null> $types
     *
     * @throws \Doctrine\DBAL\Exception
     */
    public function fetchAssoc(string $statement, array $params = [], array $types = []): array
    {
        $result = $this->executeQuery($statement, $params, $types)->fetchAssociative();

        return $result !== false ? $result : [];
    }

    /**
     * @param array<int, int|string|Type|null>|array<string, int|string|Type|null> $types
     *
     * @throws \Doctrine\DBAL\Exception
     */
    public function fetchColumn(string $statement, array $params = [], array $types = []): bool|string
    {
        return $this->executeQuery($statement, $params, $types)->fetchOne();
    }

    /**
     * @param array<int, int|string|Type|null>|array<string, int|string|Type|null> $types
     *
     * @throws \Doctrine\DBAL\Exception
     */
    public function executeQuery(
        string $query,
        array $params = [],
        array $types = [],
        bool $cache = false,
        int $lifetime = 0,
        ?string $cacheKey = null
    ): Result {
        return $this->getConnection()->executeQuery(
            $query,
            $params,
            $types,
            $cache ? new QueryCacheProfile($lifetime, $cacheKey ?: md5($query)) : null
        );
    }

    /**
     * @throws \Doctrine\DBAL\Exception
     */
    protected function connect(): DBAL\Connection
    {
        $config = new DBAL\Configuration();
        $config->setAutoCommit(false);

        if ($this->appMode !== ApplicationMode::PRODUCTION) {
            $config->setMiddlewares([new DBAL\Logging\Middleware($this->logger)]);
        }

        return DBAL\DriverManager::getConnection($this->connectionParams, $config);
    }
}
