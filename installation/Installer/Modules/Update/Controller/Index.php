<?php

namespace ACP3\Installer\Modules\Update\Controller;

use ACP3\Core\Cache;
use ACP3\Core\Modules;
use ACP3\Installer\Core;

/**
 * Class Index
 * @package ACP3\Installer\Modules\Update\Controller
 */
class Index extends Core\Modules\Controller
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
     * @var \ACP3\Core\Modules\Vendors
     */
    protected $vendors;

    /**
     * @param \ACP3\Installer\Core\Modules\Controller\Context $context
     * @param \ACP3\Core\Modules                              $modules
     * @param \ACP3\Core\Modules\Vendors                      $vendors
     * @param \ACP3\Core\Modules\SchemaUpdater                $schemaUpdater
     */
    public function __construct(
        Core\Modules\Controller\Context $context,
        Modules $modules,
        Modules\Vendors $vendors,
        Modules\SchemaUpdater $schemaUpdater
    )
    {
        parent::__construct($context);

        $this->modules = $modules;
        $this->vendors = $vendors;
        $this->schemaUpdater = $schemaUpdater;
    }

    public function actionIndex()
    {
        if ($this->request->getPost()->isEmpty() === false) {
            $this->_indexPost();
        }
    }

    private function _indexPost()
    {
        $results = [];

        // Zuerst die wichtigen System-Module aktualisieren...
        $coreModules = ['system', 'permissions', 'users'];
        foreach ($coreModules as $module) {
            $results[$module] = $this->_returnModuleUpdateResult($module);
        }

        // ...danach die Restlichen
        foreach ($this->vendors->getVendors() as $vendor) {
            $modules = array_diff(scandir(MODULES_DIR . $vendor . '/'), ['.', '..', '.gitignore', '.svn', '.htaccess', '.htpasswd']);
            foreach ($modules as $module) {
                if (in_array(strtolower($module), $coreModules) === false) {
                    $results[$module] = $this->_returnModuleUpdateResult($module);
                }
            }
        }

        ksort($results);

        $this->view->assign('results', $results);

        $this->_clearCaches();
    }

    /**
     * @param string $moduleName
     *
     * @return array
     */
    protected function _returnModuleUpdateResult($moduleName)
    {
        $result = $this->_updateModule($moduleName);

        return [
            'text' => sprintf($this->lang->t('update', 'db_update_text'), ucfirst($moduleName)),
            'class' => $result === 1 ? 'success' : ($result === 0 ? 'danger' : 'info'),
            'result_text' => $this->lang->t('update', $result === 1 ? 'db_update_success' : ($result === 0 ? 'db_update_error' : 'db_update_no_update'))
        ];
    }

    /**
     * Führt die Updateanweisungen eines Moduls aus
     *
     * @param string $module
     *
     * @return integer
     */
    protected function _updateModule($module)
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

    protected function _clearCaches()
    {
        Cache::purge(CACHE_DIR . 'sql');
        Cache::purge(CACHE_DIR . 'tpl_compiled');
        Cache::purge(UPLOADS_DIR . 'assets');
    }
}
