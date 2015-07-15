<?php

namespace ACP3\Installer\Modules\Install\Helpers;

use ACP3\Core;
use Symfony\Component\DependencyInjection\ContainerInterface;
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
     * @param array  $data
     *
     * @return bool
     */
    public function writeConfigFile($configFilePath, array $data)
    {
        if (is_writable($configFilePath) === true) {
            ksort($data);

            $dumper = new Dumper();
            $yaml = $dumper->dump($data);

            return file_put_contents($configFilePath, $yaml, LOCK_EX) !== false;
        }

        return false;
    }

    /**
     * @param string                                                    $module
     * @param \Symfony\Component\DependencyInjection\ContainerInterface $container
     *
     * @return bool
     */
    public function installModule($module, ContainerInterface $container)
    {
        $bool = false;
        $serviceId = $module . '.installer.schema';

        if ($container->has($serviceId)) {
            /** @var Core\Modules\Installer\SchemaInterface $moduleSchema */
            $moduleSchema = $container->get($serviceId);

            $bool = $container->get('core.modules.schemaInstaller')->install($moduleSchema);
        }

        return $bool;
    }

    /**
     * @param array         $queries
     * @param \ACP3\Core\DB $db
     *
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
