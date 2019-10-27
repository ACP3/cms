<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Installer\Modules\Install\Helpers;

use ACP3\Core\Component\ComponentRegistry;
use ACP3\Core\Installer\SchemaRegistrar;
use ACP3\Core\XML;
use Symfony\Component\DependencyInjection\ContainerInterface;

class ModuleInstaller
{
    /**
     * @var XML
     */
    protected $xml;
    /**
     * @var Install
     */
    protected $installHelper;
    /**
     * @var array
     */
    protected $installedModules = [];

    public function __construct(
        XML $xml,
        Install $installHelper
    ) {
        $this->xml = $xml;
        $this->installHelper = $installHelper;
    }

    /**
     * @param ContainerInterface $container
     *
     * @return array
     *
     * @throws \Exception
     */
    public function installModules(ContainerInterface $container): array
    {
        /** @var SchemaRegistrar $schemaRegistrar */
        $schemaRegistrar = $container->get('core.installer.schema_registrar');

        foreach (ComponentRegistry::getAllComponentsTopSorted() as $module) {
            $moduleConfigPath = $module->getPath() . '/Resources/config/module.xml';

            if (!$this->isValidModule($moduleConfigPath)) {
                continue;
            }

            if (!$schemaRegistrar->has($module->getName())) {
                continue;
            }

            if ($this->installHelper->installModule($schemaRegistrar->get($module->getName()), $container) === false) {
                throw new \Exception(
                    \sprintf('Error while installing module "%s"', $module->getName())
                );
            }

            $this->installedModules[$module->getName()] = true;
        }

        return $this->installedModules;
    }

    /**
     * @param string $moduleConfigPath
     *
     * @return bool
     */
    private function isValidModule(string $moduleConfigPath): bool
    {
        if (\is_file($moduleConfigPath)) {
            $config = $this->xml->parseXmlFile($moduleConfigPath, '/module/info');

            return !isset($config['no_install']);
        }

        return false;
    }
}
