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
     * @var DBAL\Connection
     */
    protected $connection;
    /**
     * @var string
     */
    protected $name = '';
    /**
     * @var string
     */
    protected $prefix = '';

    /**
     * @param        $dbHost
     * @param        $dbName
     * @param        $dbUser
     * @param        $dbPassword
     * @param string $dbTablePrefix
     * @param string $dbDriver
     * @param string $dbCharset
     * @param string $cacheDriverName
     *
     * @throws \Doctrine\DBAL\DBALException
     */
    public function __construct(
        $dbHost,
        $dbName,
        $dbUser,
        $dbPassword,
        $dbTablePrefix = '',
        $dbDriver = 'pdo_mysql',
        $dbCharset = 'utf8',
        $cacheDriverName = 'Array'
    )
    {
        $config = new DBAL\Configuration();
        $connectionParams = [
            'dbname' => $dbName,
            'user' => $dbUser,
            'password' => $dbPassword,
            'host' => $dbHost,
            'driver' => $dbDriver,
            'charset' => $dbCharset
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

        $this->prefix = $dbTablePrefix;
        $this->name = $dbName;
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
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getPrefix()
    {
        return $this->prefix;
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
}
