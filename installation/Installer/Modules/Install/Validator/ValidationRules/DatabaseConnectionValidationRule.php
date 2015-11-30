<?php
namespace ACP3\Installer\Modules\Install\Validator\ValidationRules;

use ACP3\Core\Validator\ValidationRules\AbstractValidationRule;

/**
 * Class DatabaseConnectionValidationRule
 * @package ACP3\Installer\Modules\Install\Validator\ValidationRules
 */
class DatabaseConnectionValidationRule extends AbstractValidationRule
{
    const NAME = 'installer_database_connection';

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
                return false;
            }
        }

        return false;
    }
}