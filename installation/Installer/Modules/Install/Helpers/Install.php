<?php

namespace ACP3\Installer\Modules\Install\Helpers;

use ACP3\Core;
use ACP3\Core\Installer\Helper\SchemaHelper;
use Psr\Container\ContainerInterface;
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
     * @param \ACP3\Core\Installer\SchemaInterface $schema
     * @param ContainerInterface $container
     * @return bool
     */
    public function installModule(Core\Installer\SchemaInterface $schema, ContainerInterface $container)
    {
        return $this->install($schema, $container, 'core.installer.schema_installer');
    }

    /**
     * @param \ACP3\Core\Installer\SchemaInterface $schema
     * @param ContainerInterface $container
     * @return bool
     */
    public function installResources(Core\Installer\SchemaInterface $schema, ContainerInterface $container)
    {
        return $this->install($schema, $container, 'core.installer.acl_installer');
    }

    /**
     * @param \ACP3\Core\Installer\SchemaInterface $schema
     * @param ContainerInterface $container
     * @param string $installerServiceId
     * @return bool
     */
    private function install(
        Core\Installer\SchemaInterface $schema,
        ContainerInterface $container,
        $installerServiceId
    ) {
        return $container->get($installerServiceId)->install($schema);
    }

    /**
     * @param \ACP3\Core\Installer\SampleDataInterface $sampleData
     * @param SchemaHelper $schemaHelper
     * @return bool
     */
    public function installSampleData(
        Core\Installer\SampleDataInterface $sampleData,
        SchemaHelper $schemaHelper
    ) {
        return $schemaHelper->executeSqlQueries($sampleData->sampleData());
    }
}
