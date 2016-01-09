<?php
namespace ACP3\Core;

use ACP3\Core\DB\SQLLogger;
use ACP3\Core\Environment\ApplicationMode;
use ACP3\Core\Environment\ApplicationPath;
use Doctrine\DBAL;
use Doctrine\DBAL\Cache\QueryCacheProfile;

/**
 * Class DB
 * @package ACP3\Core
 */
class DB
{
    /**
     * @var \ACP3\Core\Logger
     */
    protected $logger;
    /**
     * @var \ACP3\Core\Environment\ApplicationPath
     */
    protected $appPath;
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
    protected $cacheDriverName = '';
    /**
     * @var string
     */
    protected $prefix = '';

    /**
     * @param \ACP3\Core\Logger                      $logger
     * @param \ACP3\Core\Environment\ApplicationPath $appPath
     * @param string                                 $appMode
     * @param array                                  $connectionParams
     * @param string                                 $tablePrefix
     * @param string                                 $cacheDriverName
     */
    public function __construct(
        Logger $logger,
        ApplicationPath $appPath,
        $appMode,
        array $connectionParams,
        $tablePrefix,
        $cacheDriverName
    ) {
        $this->logger = $logger;
        $this->appPath = $appPath;
        $this->appMode = $appMode;
        $this->connectionParams = $connectionParams;
        $this->prefix = $tablePrefix;
        $this->cacheDriverName = $cacheDriverName;

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
        if ($cache === false) {
            return $this->connection->executeQuery($query, $params, $types);
        }

        return $this->connection->executeCacheQuery(
            $query,
            $params,
            $types,
            new QueryCacheProfile($lifetime, $cacheKey ?: md5($query))
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
            $this->logger->error('database', $e->getMessage());
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

        $this->applyQueryCache($config);

        return DBAL\DriverManager::getConnection($this->connectionParams, $config);
    }

    /**
     * @param \Doctrine\DBAL\Configuration $config
     */
    protected function applyQueryCache(DBAL\Configuration $config)
    {
        $className = "\\Doctrine\\Common\\Cache\\" . $this->cacheDriverName . "Cache";
        /** @var \Doctrine\Common\Cache\CacheProvider $cacheDriver */
        if (strtolower($this->cacheDriverName) === 'phpfile') {
            $cacheDriver = new $className($this->appPath->getCacheDir() . 'sql/');
        } else {
            $cacheDriver = new $className();
        }

        $cacheDriver->setNamespace('db-queries');

        $config->setResultCacheImpl($cacheDriver);
    }
}
