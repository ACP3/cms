<?php
/**
 * Copyright (c) 2016 by the ACP3 Developers.
 * See the LICENCE file at the top-level module directory for licencing details.
 */

namespace ACP3\Installer\Modules\Install\Controller\Install;


use ACP3\Installer\Core\Controller\AbstractInstallerAction;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\DBALException;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * Class AvailableDatabases
 * @package ACP3\Installer\Modules\Install\Controller\Install
 */
class AvailableDatabases extends AbstractInstallerAction
{
    /**
     * @return JsonResponse
     */
    public function execute()
    {
        $availableDatabases = [];
        if (!$this->request->getPost()->isEmpty()) {
            $hostName = $this->request->getPost()->get('db_host', '');
            $userName = $this->request->getPost()->get('db_user', '');
            $password = $this->request->getPost()->get('db_password', '');

            $conn = $this->getDatabaseConnection($hostName, $userName, $password);
            if ($conn instanceof Connection) {
                $availableDatabases = $this->retrieveAvailableDatabases($conn);
            }
        }

        return new JsonResponse($availableDatabases);
    }

    /**
     * @param string $hostname
     * @param string $userName
     * @param string $password
     * @return \Doctrine\DBAL\Connection|null
     */
    private function getDatabaseConnection($hostname, $userName, $password)
    {
        try {
            $config = new \Doctrine\DBAL\Configuration();

            $connectionParams = [
                'user' => $userName,
                'password' => $password,
                'host' => $hostname,
                'driver' => 'pdo_mysql',
                'charset' => 'utf8'
            ];

            return \Doctrine\DBAL\DriverManager::getConnection($connectionParams, $config);
        } catch (DBALException $e) {
            return null;
        }
    }

    /**
     * @param Connection $conn
     * @return array
     */
    private function retrieveAvailableDatabases(Connection $conn)
    {
        $availableDatabases = [];
        $databases = $conn->fetchAll('SHOW DATABASES');
        foreach ($databases as $database) {
            $availableDatabases[] = $database['Database'];
        }
        return array_values(array_diff($availableDatabases, $this->getMySQLDefaultDatabases()));
    }

    /**
     * @return array
     */
    private function getMySQLDefaultDatabases()
    {
        return ['information_schema', 'performance_schema', 'mysql', 'test'];
    }
}
