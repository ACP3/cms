<?php

namespace ACP3\Installer\Modules\Install;

use ACP3\Core;
use Symfony\Component\DependencyInjection\Container;

/**
 * Class Helpers
 * @package ACP3\Installer\Modules\Install
 */
class Helpers
{
    /**
     * Writes the system config file
     *
     * @param       $configFilePath
     * @param array $data
     *
     * @return bool
     */
    public function writeConfigFile($configFilePath, array $data)
    {
        if (is_writable($configFilePath) === true) {
            // Konfigurationsdatei in ein Array schreiben
            ksort($data);

            $content = "<?php\n";
            $content .= "define('INSTALLED', true);\n";

            $pattern = "define('CONFIG_%s', %s);\n";
            foreach ($data as $key => $value) {
                if (is_bool($value) === true) {
                    $value = $value === true ? 'true' : 'false';
                } elseif (is_numeric($value) !== true) {
                    $value = '\'' . $value . '\'';
                }
                $content .= sprintf($pattern, strtoupper($key), $value);
            }
            $bool = @file_put_contents($configFilePath, $content, LOCK_EX);
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

    public function executeSqlQueries(array $queries, \Doctrine\DBAL\Connection $db)
    {
        if (count($queries) > 0) {
            $search = array('{pre}', '{engine}', '{charset}');
            $replace = array(DB_PRE, 'ENGINE=MyISAM', 'CHARACTER SET `utf8` COLLATE `utf8_general_ci`');

            $db->beginTransaction();
            try {
                foreach ($queries as $query) {
                    if (!empty($query)) {
                        $db->query(str_replace($search, $replace, $query));
                    }
                }
                $db->commit();
            } catch (\Exception $e) {
                $db->rollBack();

                Core\Logger::warning('installer', $e);
                return false;
            }
        }
        return true;
    }
}