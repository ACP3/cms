<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Installer\Helpers;

use ACP3\Core\Component\ComponentRegistry;
use ACP3\Core\Installer\SchemaRegistrar;
use ACP3\Core\XML;
use ACP3\Modules\ACP3\System\Exception\ModuleInstallerException;
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
     * @throws \ACP3\Modules\ACP3\System\Exception\ModuleInstallerException
     * @throws \MJS\TopSort\CircularDependencyException
     * @throws \MJS\TopSort\ElementNotFoundException
     */
    public function installModules(ContainerInterface $container): array
    {
        /** @var SchemaRegistrar $schemaRegistrar */
        $schemaRegistrar = $container->get('core.installer.schema_registrar');

        foreach (ComponentRegistry::allTopSorted() as $module) {
            $moduleConfigPath = $module->getPath() . '/Resources/config/module.xml';

            if (!$this->isValidModule($moduleConfigPath)) {
                continue;
            }

            if (!$schemaRegistrar->has($module->getName())) {
                continue;
            }

            if ($this->installHelper->installModule($schemaRegistrar->get($module->getName()), $container) === false) {
                throw new ModuleInstallerException(\sprintf('Error while installing module "%s"', $module->getName()));
            }

            $this->installedModules[$module->getName()] = true;
        }

        return $this->installedModules;
    }

    private function isValidModule(string $moduleConfigPath): bool
    {
        if (\is_file($moduleConfigPath)) {
            $config = $this->xml->parseXmlFile($moduleConfigPath, '/module/info');

            return !isset($config['no_install']);
        }

        return false;
    }
}
