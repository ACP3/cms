<?php
/**
 * Copyright (c) 2016 by the ACP3 Developers.
 * See the LICENCE file at the top-level module directory for licencing details.
 */

namespace ACP3\Installer\Modules\Install\Validation\ValidationRules;

use ACP3\Core\Validation\ValidationRules\AbstractValidationRule;

/**
 * Class DatabaseConnectionValidationRule
 * @package ACP3\Installer\Modules\Install\Validation\ValidationRules
 */
class DatabaseConnectionValidationRule extends AbstractValidationRule
{
    /**
     * @inheritdoc
     */
    public function isValid($data, $field = '', array $extra = [])
    {
        if (is_array($data) && is_array($field)) {
            $dbHost = reset($field);
            $dbUser = next($field);
            $dbPassword = next($field);
            $dbName = next($field);

            try {
                $config = new \Doctrine\DBAL\Configuration();

                $connectionParams = [
                    'dbname' => $data[$dbName],
                    'user' => $data[$dbUser],
                    'password' => $data[$dbPassword],
                    'host' => $data[$dbHost],
                    'driver' => 'pdo_mysql',
                    'charset' => 'utf8'
                ];
                $db = \Doctrine\DBAL\DriverManager::getConnection($connectionParams, $config);
                $db->query('USE `' . $data[$dbName] . '`');

                return true;
            } catch (\Exception $e) {
                $this->setMessage(sprintf($this->getMessage(), $e->getMessage()));
                return false;
            }
        }

        return false;
    }
}
