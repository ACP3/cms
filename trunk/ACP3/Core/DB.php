<?php
namespace ACP3\Core;

use \Doctrine\DBAL;

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
     * @param $dbHost
     * @param $dbName
     * @param $dbUser
     * @param $dbPassword
     * @param string $dbTablePrefix
     * @param string $dbDriver
     * @param string $dbCharset
     * @throws DBAL\DBALException
     */
    public function __construct(
        $dbHost,
        $dbName,
        $dbUser,
        $dbPassword,
        $dbTablePrefix = '',
        $dbDriver = 'pdo_mysql',
        $dbCharset = 'utf8'
    )
    {
        $config = new DBAL\Configuration();
        $connectionParams = array(
            'dbname' => $dbName,
            'user' => $dbUser,
            'password' => $dbPassword,
            'host' => $dbHost,
            'driver' => $dbDriver,
            'charset' => $dbCharset
        );
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

}