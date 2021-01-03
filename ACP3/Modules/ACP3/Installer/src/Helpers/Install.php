<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Installer\Helpers;

use ACP3\Core;
use ACP3\Core\Modules\Installer\SampleDataInterface;
use ACP3\Core\Modules\Installer\SchemaInterface;
use ACP3\Core\Modules\SchemaHelper;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Yaml\Dumper;

class Install
{
    /**
     * Writes the system config file.
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
     */
    public function installModule(SchemaInterface $schema, ContainerInterface $container): bool
    {
        return $this->install($schema, $container, 'core.modules.schemaInstaller');
    }

    /**
     * @param Core\Modules\Installer\SchemaInterface $schema
     */
    public function installResources(SchemaInterface $schema, ContainerInterface $container): bool
    {
        return $this->install($schema, $container, 'core.modules.aclInstaller');
    }

    /**
     * @param Core\Modules\Installer\SchemaInterface $schema
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
     * @throws \Doctrine\DBAL\ConnectionException
     * @throws \Doctrine\DBAL\DBALException
     */
    public function installSampleData(
        SampleDataInterface $sampleData,
        SchemaHelper $schemaHelper
    ): void {
        $schemaHelper->executeSqlQueries($sampleData->sampleData());
    }
}
