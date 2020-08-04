<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Core\Database;

use ACP3\Core\Cache\CacheDriverFactory;
use ACP3\Core\Environment\ApplicationMode;
use Doctrine\DBAL;
use Doctrine\DBAL\Cache\QueryCacheProfile;
use Psr\Log\LoggerInterface;

class Connection
{
    /**
     * @var LoggerInterface
     */
    private $logger;
    /**
     * @var \ACP3\Core\Cache\CacheDriverFactory
     */
    private $cacheDriverFactory;
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
        CacheDriverFactory $cacheDriverFactory,
        string $appMode,
        array $connectionParams,
        string $tablePrefix
    ) {
        $this->logger = $logger;
        $this->cacheDriverFactory = $cacheDriverFactory;
        $this->appMode = $appMode;
        $this->connectionParams = $connectionParams;
        $this->prefix = $tablePrefix;
    }

    /**
     * @throws \Doctrine\DBAL\DBALException
     */
    public function getConnection(): DBAL\Connection
    {
        if ($this->connection === null) {
            $this->connection = $this->connect();
        }

        return $this->connection;
    }

    /**
     * @return \Doctrine\DBAL\Driver\Connection|null
     *
     * @throws \Doctrine\DBAL\DBALException
     */
    public function getWrappedConnection(): ?DBAL\Driver\Connection
    {
        return $this->getConnection()->getWrappedConnection();
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
     * @throws \Doctrine\DBAL\DBALException
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
        $data = $stmt->fetchAll();
        $stmt->closeCursor();

        return $data;
    }

    /**
     * @return mixed
     *
     * @throws \Doctrine\DBAL\DBALException
     */
    public function fetchArray(string $statement, array $params = [], array $types = [])
    {
        return $this->executeQuery($statement, $params, $types)->fetch(\PDO::FETCH_BOTH);
    }

    /**
     * @throws \Doctrine\DBAL\DBALException
     */
    public function fetchAssoc(string $statement, array $params = [], array $types = []): array
    {
        $result = $this->executeQuery($statement, $params, $types)->fetch(\PDO::FETCH_ASSOC);

        return $result !== false ? $result : [];
    }

    /**
     * @return bool|string
     *
     * @throws \Doctrine\DBAL\DBALException
     */
    public function fetchColumn(string $statement, array $params = [], int $column = 0, array $types = [])
    {
        return $this->executeQuery($statement, $params, $types)->fetchColumn($column);
    }

    /**
     * @return \Doctrine\DBAL\Driver\ResultStatement|\Doctrine\DBAL\Driver\Statement
     *
     * @throws \Doctrine\DBAL\DBALException
     */
    public function executeQuery(
        string $query,
        array $params = [],
        array $types = [],
        bool $cache = false,
        int $lifetime = 0,
        ?string $cacheKey = null
    ) {
        return $this->getConnection()->executeQuery(
            $query,
            $params,
            $types,
            $cache ? new QueryCacheProfile($lifetime, $cacheKey ?: \md5($query)) : null
        );
    }

    /**
     * @return mixed
     *
     * @throws DBAL\ConnectionException
     * @throws DBAL\DBALException
     */
    public function executeTransactionalQuery(callable $callback)
    {
        $this->getConnection()->beginTransaction();

        try {
            $result = $callback();

            $this->getConnection()->commit();
        } catch (DBAL\DBALException $e) {
            $this->getConnection()->rollBack();

            throw $e;
        }

        return $result;
    }

    /**
     * @throws \Doctrine\DBAL\DBALException
     */
    protected function connect(): DBAL\Connection
    {
        $config = new DBAL\Configuration();
        if ($this->appMode === ApplicationMode::DEVELOPMENT) {
            $config->setSQLLogger(new SQLLogger($this->logger));
        }

        $config->setResultCacheImpl($this->cacheDriverFactory->create('db-queries'));

        return DBAL\DriverManager::getConnection($this->connectionParams, $config);
    }
}
