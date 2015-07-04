<?php

namespace ACP3\Installer\Modules\Install\Helpers;

use ACP3\Core;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\Yaml\Dumper;

/**
 * Class Install
 * @package ACP3\Installer\Modules\Install\Helpers
 */
class Install
{
    /**
     * Writes the system config file
     *
     * @param string $configFilePath
     * @param array $data
     *
     * @return bool
     */
    public function writeConfigFile($configFilePath, array $data)
    {
        if (is_writable($configFilePath) === true) {
            // Konfigurationsdatei in ein Array schreiben
            ksort($data);

            $dumper = new Dumper();

            $yaml = $dumper->dump($data);

            $bool = @file_put_contents($configFilePath, $yaml, LOCK_EX);
            return $bool !== false;
        }
        return false;
    }

    /**
     * @param           $module
     * @param Container $container
     *
     * @return bool
     */
    public function installModule($module, Container $container)
    {
        $bool = false;
        $serviceId = $module . '.installer';

        if ($container->has($serviceId)) {
            /** @var \ACP3\Core\Modules\AbstractInstaller $installer */
            $installer = $container->get($serviceId);

            $bool = $installer->install();
        }

        return $bool;
    }

    /**
     * @param array $queries
     * @param Core\DB $db
     * @return bool
     * @throws \Doctrine\DBAL\ConnectionException
     */
    public function executeSqlQueries(array $queries, Core\DB $db)
    {
        if (count($queries) > 0) {
            $search = ['{pre}', '{engine}', '{charset}'];
            $replace = [$db->getPrefix(), 'ENGINE=MyISAM', 'CHARACTER SET `utf8` COLLATE `utf8_general_ci`'];

            $db->getConnection()->beginTransaction();
            try {
                foreach ($queries as $query) {
                    if (!empty($query)) {
                        $db->getConnection()->query(str_replace($search, $replace, $query));
                    }
                }
                $db->getConnection()->commit();
            } catch (\Exception $e) {
                $db->getConnection()->rollBack();

                Core\Logger::warning('installer', $e);
                return false;
            }
        }
        return true;
    }
}
