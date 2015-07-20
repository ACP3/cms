<?php

namespace ACP3\Installer\Modules\Install\Helpers;

use ACP3\Core;
use ACP3\Core\Modules\SchemaHelper;
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
     * @param string                                                    $module
     * @param \Symfony\Component\DependencyInjection\ContainerInterface $container
     * @param \ACP3\Core\Modules\SchemaHelper                           $schemaHelper
     *
     * @return bool
     */
    public function installSampleData($module, ContainerInterface $container, SchemaHelper $schemaHelper)
    {
        $bool = true;
        $serviceId = $module . '.installer.sampleData';

        if ($container->has($serviceId)) {
            /** @var Core\Modules\Installer\SampleDataInterface $moduleSampleData */
            $moduleSampleData = $container->get($serviceId);

            $bool = $schemaHelper->executeSqlQueries($moduleSampleData->sampleData());
        }

        return $bool;
    }
}
