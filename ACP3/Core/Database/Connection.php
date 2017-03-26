<?php
/**
 * Copyright (c) 2016 by the ACP3 Developers.
 * See the LICENCE file at the top-level module directory for licencing details.
 */

namespace ACP3\Core\Database;

use ACP3\Core\Cache\CacheDriverFactory;
use ACP3\Core\Environment\ApplicationMode;
use ACP3\Core\Environment\ApplicationPath;
use Doctrine\DBAL;
use Doctrine\DBAL\Cache\QueryCacheProfile;
use Psr\Log\LoggerInterface;

/**
 * Class Connection
 * @package ACP3\Core\Database
 */
class Connection
{
    /**
     * @var LoggerInterface
     */
    protected $logger;
    /**
     * @var \ACP3\Core\Environment\ApplicationPath
     */
    protected $appPath;
    /**
     * @var \ACP3\Core\Cache\CacheDriverFactory
     */
    protected $cacheDriverFactory;
    /**
     * @var \Doctrine\DBAL\Connection
     */
    protected $connection;
    /**
     * @var string
     */
    protected $appMode = '';
    /**
     * @var array
     */
    protected $connectionParams = [];
    /**
     * @var string
     */
    protected $prefix = '';

    /**
     * Connection constructor.
     * @param LoggerInterface $logger
     * @param ApplicationPath $appPath
     * @param CacheDriverFactory $cacheDriverFactory
     * @param $appMode
     * @param array $connectionParams
     * @param $tablePrefix
     */
    public function __construct(
        LoggerInterface $logger,
        ApplicationPath $appPath,
        CacheDriverFactory $cacheDriverFactory,
        $appMode,
        array $connectionParams,
        $tablePrefix
    ) {
        $this->logger = $logger;
        $this->appPath = $appPath;
        $this->cacheDriverFactory = $cacheDriverFactory;
        $this->appMode = $appMode;
        $this->connectionParams = $connectionParams;
        $this->prefix = $tablePrefix;

        $this->connection = $this->connect();
    }

    /**
     * @return DBAL\Connection
     */
    public function getConnection()
    {
        return $this->connection;
    }

    /**
     * @return string
     */
    public function getDatabase()
    {
        return $this->connectionParams['dbname'];
    }

    /**
     * @return string
     */
    public function getPrefix()
    {
        return $this->prefix;
    }

    /**
     * @param string $tableName
     *
     * @return string
     */
    public function getPrefixedTableName($tableName)
    {
        return $this->prefix . $tableName;
    }

    /**
     * @param string $statement
     * @param array  $params
     * @param array  $types
     * @param bool   $cache
     * @param int    $lifetime
     * @param null   $cacheKey
     *
     * @return array
     */
    public function fetchAll(
        $statement,
        array $params = [],
        array $types = [],
        $cache = false,
        $lifetime = 0,
        $cacheKey = null
    ) {
        $stmt = $this->executeQuery($statement, $params, $types, $cache, $lifetime, $cacheKey);
        $data = $stmt->fetchAll();
        $stmt->closeCursor();

        return $data;
    }

    /**
     * @param string $statement
     * @param array  $params
     * @param array  $types
     *
     * @return mixed
     */
    public function fetchArray($statement, array $params = [], array $types = [])
    {
        return $this->executeQuery($statement, $params, $types)->fetch(\PDO::FETCH_BOTH);
    }

    /**
     * @param string $statement
     * @param array  $params
     * @param array  $types
     *
     * @return mixed
     */
    public function fetchAssoc($statement, array $params = [], array $types = [])
    {
        return $this->executeQuery($statement, $params, $types)->fetch(\PDO::FETCH_ASSOC);
    }

    /**
     * @param string $statement
     * @param array  $params
     * @param int    $column
     * @param array  $types
     *
     * @return bool|string
     */
    public function fetchColumn($statement, array $params = [], $column = 0, array $types = [])
    {
        return $this->executeQuery($statement, $params, $types)->fetchColumn($column);
    }

    /**
     * @param string $query
     * @param array  $params
     * @param array  $types
     * @param bool   $cache
     * @param int    $lifetime
     * @param null   $cacheKey
     *
     * @return \Doctrine\DBAL\Driver\ResultStatement|\Doctrine\DBAL\Driver\Statement
     * @throws \Doctrine\DBAL\Cache\CacheException
     * @throws \Doctrine\DBAL\DBALException
     */
    public function executeQuery(
        $query,
        array $params = [],
        array $types = [],
        $cache = false,
        $lifetime = 0,
        $cacheKey = null
    ) {
        return $this->connection->executeQuery(
            $query,
            $params,
            $types,
            $cache ? new QueryCacheProfile($lifetime, $cacheKey ?: md5($query)) : null
        );
    }

    /**
     * @param callable $callback
     *
     * @return bool|int
     * @throws \Doctrine\DBAL\ConnectionException
     */
    public function executeTransactionalQuery(callable $callback)
    {
        $this->connection->beginTransaction();

        try {
            $result = $callback();

            $this->connection->commit();
        } catch (\Exception $e) {
            $this->connection->rollBack();
            $this->logger->error($e);
            $result = false;
        }

        return $result;
    }

    /**
     * @throws \Doctrine\DBAL\DBALException
     */
    protected function connect()
    {
        $config = new DBAL\Configuration();
        if ($this->appMode === ApplicationMode::DEVELOPMENT) {
            $config->setSQLLogger(new SQLLogger($this->logger));
        }

        $config->setResultCacheImpl($this->cacheDriverFactory->create('db-queries'));

        return DBAL\DriverManager::getConnection($this->connectionParams, $config);
    }
}
