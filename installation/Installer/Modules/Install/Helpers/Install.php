<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Installer\Modules\Install\Helpers;

use ACP3\Core;
use ACP3\Core\Modules\Installer\SchemaInterface;
use ACP3\Core\Modules\SchemaHelper;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Yaml\Dumper;

class Install
{
    /**
     * Writes the system config file.
     *
     * @param string $configFilePath
     * @param array  $data
     *
     * @return bool
     */
    public function writeConfigFile(string $configFilePath, array $data): bool
    {
        if (\is_writable($configFilePath) === true) {
            \ksort($data);

            $dumper = new Dumper();
            $yaml = $dumper->dump($data);

            return \file_put_contents($configFilePath, $yaml, LOCK_EX) !== false;
        }

        return false;
    }

    /**
     * @param Core\Modules\Installer\SchemaInterface $schema
     * @param ContainerInterface                     $container
     *
     * @return bool
     */
    public function installModule(SchemaInterface $schema, ContainerInterface $container): bool
    {
        return $this->install($schema, $container, 'core.modules.schemaInstaller');
    }

    /**
     * @param Core\Modules\Installer\SchemaInterface $schema
     * @param ContainerInterface                     $container
     *
     * @return bool
     */
    public function installResources(SchemaInterface $schema, ContainerInterface $container): bool
    {
        return $this->install($schema, $container, 'core.modules.aclInstaller');
    }

    /**
     * @param Core\Modules\Installer\SchemaInterface $schema
     * @param ContainerInterface                     $container
     * @param string                                 $installerServiceId
     *
     * @return bool
     */
    private function install(
        SchemaInterface $schema,
        ContainerInterface $container,
        string $installerServiceId
    ): bool {
        /** @var \ACP3\Core\Modules\InstallerInterface $installer */
        $installer = $container->get($installerServiceId);

        return $installer->install($schema);
    }

    /**
     * @param Core\Modules\Installer\SampleDataInterface $sampleData
     * @param SchemaHelper                               $schemaHelper
     *
     * @return bool
     */
    public function installSampleData(
        Core\Modules\Installer\SampleDataInterface $sampleData,
        SchemaHelper $schemaHelper
    ): bool {
        try {
            $schemaHelper->executeSqlQueries($sampleData->sampleData());

            return true;
        } catch (\Throwable $e) {
            return false;
        }
    }
}
