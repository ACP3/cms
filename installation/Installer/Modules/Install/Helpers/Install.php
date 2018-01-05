<?php

namespace ACP3\Installer\Modules\Install\Helpers;

use ACP3\Core;
use ACP3\Core\Modules\SchemaHelper;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Yaml\Dumper;

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
     * @param Core\Modules\Installer\SchemaInterface $schema
     * @param ContainerInterface $container
     * @return bool
     */
    public function installModule(Core\Modules\Installer\SchemaInterface $schema, ContainerInterface $container)
    {
        return $this->install($schema, $container, 'core.modules.schemaInstaller');
    }

    /**
     * @param Core\Modules\Installer\SchemaInterface $schema
     * @param ContainerInterface $container
     * @return bool
     */
    public function installResources(Core\Modules\Installer\SchemaInterface $schema, ContainerInterface $container)
    {
        return $this->install($schema, $container, 'core.modules.aclInstaller');
    }

    /**
     * @param Core\Modules\Installer\SchemaInterface $schema
     * @param ContainerInterface $container
     * @param string $installerServiceId
     * @return bool
     */
    private function install(
        Core\Modules\Installer\SchemaInterface $schema,
        ContainerInterface $container,
        $installerServiceId
    ) {
        return $container->get($installerServiceId)->install($schema);
    }

    /**
     * @param Core\Modules\Installer\SampleDataInterface $sampleData
     * @param SchemaHelper $schemaHelper
     * @return bool
     */
    public function installSampleData(
        Core\Modules\Installer\SampleDataInterface $sampleData,
        SchemaHelper $schemaHelper
    ) {
        return $schemaHelper->executeSqlQueries($sampleData->sampleData());
    }
}
