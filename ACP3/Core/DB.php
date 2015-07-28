<?php
namespace ACP3\Core;

use ACP3\Core\DB\SQLLogger;
use \Doctrine\DBAL;
use Doctrine\DBAL\Cache\QueryCacheProfile;

/**
 * Class DB
 * @package ACP3\Core
 */
class DB
{
    /**
     * @var \Doctrine\DBAL\Connection
     */
    protected $connection;
    /**
     * @var string
     */
    protected $database = '';
    /**
     * @var string
     */
    protected $prefix = '';

    /**
     * @param string $host
     * @param string $database
     * @param string $userName
     * @param string $password
     * @param string $tablePrefix
     * @param string $driver
     * @param string $charset
     * @param string $cacheDriverName
     *
     * @throws \Doctrine\DBAL\DBALException
     */
    public function __construct(
        $host,
        $database,
        $userName,
        $password,
        $tablePrefix = '',
        $driver = 'pdo_mysql',
        $charset = 'utf8',
        $cacheDriverName = 'Array'
    )
    {
        $config = new DBAL\Configuration();
        $connectionParams = [
            'dbname' => $database,
            'user' => $userName,
            'password' => $password,
            'host' => $host,
            'driver' => $driver,
            'charset' => $charset
        ];
        if (defined('DEBUG_SQL') === true && DEBUG_SQL === true) {
            $config->setSQLLogger(new SQLLogger());
        }

        $className = "\\Doctrine\\Common\\Cache\\" . $cacheDriverName . "Cache";
        /** @var \Doctrine\Common\Cache\CacheProvider $cacheDriverName */
        if (strtolower($cacheDriverName)) {
            $cacheDriverName = new $className(CACHE_DIR . 'sql/');
        } else {
            $cacheDriverName = new $className();
        }

        $cacheDriverName->setNamespace('db-queries');

        $config->setResultCacheImpl($cacheDriverName);

        $this->connection = DBAL\DriverManager::getConnection($connectionParams, $config);

        $this->prefix = $tablePrefix;
        $this->database = $database;
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
        return $this->database;
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
     * @param       $statement
     * @param array $params
     * @param array $types
     * @param bool  $cache
     * @param int   $lifetime
     * @param null  $cacheKey
     *
     * @return array
     */
    public function fetchAll($statement, array $params = [], array $types = [], $cache = false, $lifetime = 0, $cacheKey = null)
    {
        $stmt = $this->executeQuery($statement, $params, $types, $cache, $lifetime, $cacheKey);
        $data = $stmt->fetchAll();
        $stmt->closeCursor();

        return $data;
    }

    /**
     * @param       $statement
     * @param array $params
     * @param array $types
     *
     * @return mixed
     */
    public function fetchArray($statement, array $params = [], array $types = [])
    {
        return $this->executeQuery($statement, $params, $types)->fetch(\PDO::FETCH_BOTH);
    }

    /**
     * @param       $statement
     * @param array $params
     * @param array $types
     *
     * @return mixed
     */
    public function fetchAssoc($statement, array $params = [], array $types = [])
    {
        return $this->executeQuery($statement, $params, $types)->fetch(\PDO::FETCH_ASSOC);
    }

    /**
     * @param       $statement
     * @param array $params
     * @param int   $column
     * @param array $types
     *
     * @return bool|string
     */
    public function fetchColumn($statement, array $params = [], $column = 0, array $types = [])
    {
        return $this->executeQuery($statement, $params, $types)->fetchColumn($column);
    }

    /**
     * @param       $query
     * @param array $params
     * @param array $types
     * @param bool  $cache
     * @param int   $lifetime
     * @param null  $cacheKey
     *
     * @return \Doctrine\DBAL\Driver\ResultStatement|\Doctrine\DBAL\Driver\Statement
     * @throws \Doctrine\DBAL\Cache\CacheException
     * @throws \Doctrine\DBAL\DBALException
     */
    public function executeQuery($query, array $params = [], array $types = [], $cache = false, $lifetime = 0, $cacheKey = null)
    {
        if ($cache === false) {
            return $this->connection->executeQuery($query, $params, $types);
        }

        return $this->connection->executeCacheQuery($query, $params, $types, new QueryCacheProfile($lifetime, $cacheKey ?: md5($query)));
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
            Logger::error('database', $e->getMessage());
            $result = false;
        }

        return $result;
    }
}
