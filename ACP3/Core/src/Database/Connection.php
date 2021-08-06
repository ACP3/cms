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
use Psr\Log\LoggerInterface;

class Connection
{
    /**
     * @var LoggerInterface
     */
    private $logger;
    /**
     * @var \Doctrine\DBAL\Connection
     */
    private $connection;
    /**
     * @var string
     */
    private $appMode;
    /**
     * @var array
     */
    private $connectionParams;
    /**
     * @var string
     */
    private $prefix;

    public function __construct(
        LoggerInterface $logger,
        string $appMode,
        array $connectionParams,
        string $tablePrefix
    ) {
        $this->logger = $logger;
        $this->appMode = $appMode;
        $this->connectionParams = $connectionParams;
        $this->prefix = $tablePrefix;
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
        return $this->prefix;
    }

    public function getPrefixedTableName(string $tableName): string
    {
        return $this->prefix . $tableName;
    }

    /**
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
     * @throws \Doctrine\DBAL\Exception
     */
    public function fetchAssoc(string $statement, array $params = [], array $types = []): array
    {
        $result = $this->executeQuery($statement, $params, $types)->fetchAssociative();

        return $result !== false ? $result : [];
    }

    /**
     * @return bool|string
     *
     * @throws \Doctrine\DBAL\Exception
     */
    public function fetchColumn(string $statement, array $params = [], array $types = [])
    {
        return $this->executeQuery($statement, $params, $types)->fetchOne();
    }

    /**
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
     * @return mixed
     *
     * @throws DBAL\ConnectionException
     * @throws DBAL\Exception
     */
    public function executeTransactionalQuery(callable $callback)
    {
        $this->getConnection()->beginTransaction();

        try {
            $result = $callback();

            $this->getConnection()->commit();
        } catch (DBAL\Exception $e) {
            $this->getConnection()->rollBack();

            throw $e;
        }

        return $result;
    }

    /**
     * @throws \Doctrine\DBAL\Exception
     */
    protected function connect(): DBAL\Connection
    {
        $config = new DBAL\Configuration();
        if ($this->appMode === ApplicationMode::DEVELOPMENT) {
            $config->setSQLLogger(new SQLLogger($this->logger));
        }

        return DBAL\DriverManager::getConnection($this->connectionParams, $config);
    }
}
