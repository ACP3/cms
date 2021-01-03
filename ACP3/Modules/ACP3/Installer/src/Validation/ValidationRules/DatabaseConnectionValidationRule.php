<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Installer\Validation\ValidationRules;

use ACP3\Core\Validation\ValidationRules\AbstractValidationRule;
use Doctrine\DBAL\Configuration;
use Doctrine\DBAL\DriverManager;

class DatabaseConnectionValidationRule extends AbstractValidationRule
{
    /**
     * {@inheritdoc}
     */
    public function isValid($data, $field = '', array $extra = [])
    {
        if (\is_array($data) && \is_array($field)) {
            $dbHost = \reset($field);
            $dbUser = \next($field);
            $dbPassword = \next($field);
            $dbName = \next($field);

            try {
                $config = new Configuration();

                $connectionParams = [
                    'dbname' => $data[$dbName],
                    'user' => $data[$dbUser],
                    'password' => $data[$dbPassword],
                    'host' => $data[$dbHost],
                    'driver' => 'pdo_mysql',
                    'charset' => 'utf8',
                ];
                $db = DriverManager::getConnection($connectionParams, $config);
                $db->query('USE `' . $data[$dbName] . '`');

                return true;
            } catch (\Exception $e) {
                $this->setMessage(\sprintf($this->getMessage(), $e->getMessage()));
            }
        }

        return false;
    }
}
