<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Core\Installer\Model;

use ACP3\Core\Installer\Exception\ModuleNotInstallableException;
use ACP3\Core\Migration\Exception\NoExistingModuleMigrationsException;
use ACP3\Core\Migration\Migrator;
use ACP3\Core\Modules;

class SchemaUpdateModel
{
    /**
     * @var \ACP3\Core\Modules
     */
    private $modules;
    /**
     * @var Migrator
     */
    private $migrator;

    public function __construct(
        Modules $modules,
        Migrator $migrator
    ) {
        $this->modules = $modules;
        $this->migrator = $migrator;
    }

    /**
     * @throws \MJS\TopSort\CircularDependencyException
     * @throws \MJS\TopSort\ElementNotFoundException
     */
    public function updateModules(): array
    {
        $results = [];

        foreach ($this->modules->getAllModulesTopSorted() as $moduleInfo) {
            $moduleName = strtolower($moduleInfo['name']);

            try {
                $this->updateModule($moduleName);

                $results[$moduleName] = true;
            } catch (ModuleNotInstallableException $e) {
                // Intentionally omitted
            } catch (NoExistingModuleMigrationsException $e) {
                $results[$moduleName] = true;
            } catch (\Throwable $e) {
                $results[$moduleName] = false;
            }
        }

        return $results;
    }

    /**
     * Führt die Updateanweisungen eines Moduls aus.
     */
    private function updateModule(string $moduleName): void
    {
        if (!$this->modules->isInstallable($moduleName)) {
            throw new ModuleNotInstallableException(sprintf('The module %s doesn\'t need to be installed, therefore it can\'t have DB migrations.', $moduleName));
        }

        $this->migrator->updateModule($moduleName);
    }
}
