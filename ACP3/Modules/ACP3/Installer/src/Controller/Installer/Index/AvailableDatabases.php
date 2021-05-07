<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Installer\Controller\Installer\Index;

use ACP3\Core\Controller\InvokableActionInterface;
use ACP3\Modules\ACP3\Installer\Core\Controller\AbstractInstallerAction;
use Doctrine\DBAL\Configuration;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\DriverManager;
use Doctrine\DBAL\Exception as DBALException;
use Symfony\Component\HttpFoundation\JsonResponse;

class AvailableDatabases extends AbstractInstallerAction implements InvokableActionInterface
{
    public function __invoke(): JsonResponse
    {
        $availableDatabases = [];
        if ($this->request->getPost()->count() > 0) {
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

    private function getDatabaseConnection(string $hostname, string $userName, string $password): ?Connection
    {
        try {
            $config = new Configuration();

            $connectionParams = [
                'user' => $userName,
                'password' => $password,
                'host' => $hostname,
                'driver' => 'pdo_mysql',
                'charset' => 'utf8mb4',
            ];

            return DriverManager::getConnection($connectionParams, $config);
        } catch (\Throwable $e) {
            return null;
        }
    }

    private function retrieveAvailableDatabases(Connection $conn): array
    {
        $availableDatabases = [];

        try {
            $databases = $conn->fetchAllAssociative('SHOW DATABASES');
        } catch (DBALException $e) {
            $databases = [];
        }

        foreach ($databases as $database) {
            $availableDatabases[] = $database['Database'];
        }

        return array_values(array_diff($availableDatabases, $this->getMySQLDefaultDatabases()));
    }

    private function getMySQLDefaultDatabases(): array
    {
        return ['information_schema', 'performance_schema', 'mysql', 'test'];
    }
}
