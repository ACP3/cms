<?php
/**
 * Copyright (c) 2016 by the ACP3 Developers.
 * See the LICENCE file at the top-level module directory for licencing details.
 */

namespace ACP3\Installer\Modules\Update\Controller\Index;

use ACP3\Core\Cache;
use ACP3\Core\Filesystem;
use ACP3\Core\Modules;
use ACP3\Installer\Core;

/**
 * Class Index
 * @package ACP3\Installer\Modules\Update\Controller\Index
 */
class Index extends Core\Controller\AbstractInstallerAction
{
    /**
     * @var \ACP3\Core\Modules
     */
    protected $modules;
    /**
     * @var \ACP3\Core\Modules\SchemaUpdater
     */
    protected $schemaUpdater;
    /**
     * @var \ACP3\Core\Modules\Vendor
     */
    protected $vendors;

    /**
     * @param \ACP3\Installer\Core\Controller\Context\InstallerContext $context
     * @param \ACP3\Core\Modules                                       $modules
     * @param \ACP3\Core\Modules\Vendor                                $vendors
     * @param \ACP3\Core\Modules\SchemaUpdater                         $schemaUpdater
     */
    public function __construct(
        Core\Controller\Context\InstallerContext $context,
        Modules $modules,
        Modules\Vendor $vendors,
        Modules\SchemaUpdater $schemaUpdater
    ) {
        parent::__construct($context);

        $this->modules = $modules;
        $this->vendors = $vendors;
        $this->schemaUpdater = $schemaUpdater;
    }

    public function execute()
    {
        if ($this->request->getPost()->get('action') === 'confirmed') {
            $this->executePost();
        }
    }

    private function executePost()
    {
        $results = [];

        // Zuerst die wichtigen System-Module aktualisieren...
        $coreModules = ['system', 'users', 'permissions'];
        foreach ($coreModules as $module) {
            $results[$module] = $this->returnModuleUpdateResult($module);
        }

        // ...danach die Restlichen
        foreach ($this->vendors->getVendors() as $vendor) {
            foreach (Filesystem::scandir($this->appPath->getModulesDir() . $vendor . '/') as $module) {
                if (in_array(strtolower($module), $coreModules) === false) {
                    $results[$module] = $this->returnModuleUpdateResult($module);
                }
            }
        }

        ksort($results);

        $this->view->assign('results', $results);

        $this->clearCaches();
    }

    /**
     * @param string $moduleName
     *
     * @return array
     */
    protected function returnModuleUpdateResult($moduleName)
    {
        $result = $this->updateModule($moduleName);

        return [
            'text' => $this->translator->t('update', 'db_update_text', ['%module%' => ucfirst($moduleName)]),
            'class' => $result === 1 ? 'success' : ($result === 0 ? 'danger' : 'info'),
            'result_text' => $this->translator->t('update',
                $result === 1 ? 'db_update_success' : ($result === 0 ? 'db_update_error' : 'db_update_no_update'))
        ];
    }

    /**
     * Führt die Updateanweisungen eines Moduls aus
     *
     * @param string $module
     *
     * @return integer
     */
    protected function updateModule($module)
    {
        $result = false;

        $serviceIdSchema = $module . '.installer.schema';
        $serviceIdMigration = $module . '.installer.migration';
        if ($this->container->has($serviceIdSchema) === true &&
            $this->container->has($serviceIdMigration) === true) {
            /** @var Modules\Installer\SchemaInterface $moduleSchema */
            $moduleSchema = $this->container->get($serviceIdSchema);
            /** @var Modules\Installer\MigrationInterface $moduleMigration */
            $moduleMigration = $this->container->get($serviceIdMigration);
            if ($this->modules->isInstalled($module) || count($moduleMigration->renameModule()) > 0) {
                $result = $this->schemaUpdater->updateSchema($moduleSchema, $moduleMigration);
            }
        }

        return $result;
    }

    protected function clearCaches()
    {
        Cache\Purge::doPurge([
            ACP3_ROOT_DIR . 'cache/',
            $this->appPath->getUploadsDir() . 'assets'
        ]);
    }
}
