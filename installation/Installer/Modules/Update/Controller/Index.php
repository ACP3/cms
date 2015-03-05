<?php

namespace ACP3\Installer\Modules\Update\Controller;

use ACP3\Core\Cache;
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

    public function __construct(
        Core\Context $context,
        \ACP3\Core\Modules $modules
    ) {
        parent::__construct($context);

        $this->modules = $modules;
    }

    public function actionIndex()
    {
        if (empty($_POST) === false) {
            $this->_indexPost();
        }
    }

    private function _indexPost()
    {
        $results = [];

        // Zuerst die wichtigen System-Module aktualisieren...
        $coreModules = ['system', 'permissions', 'users'];
        foreach ($coreModules as $row) {
            $results[$row] = $this->_returnUpdateModuleResult($row);
        }

        // ...danach die Restlichen
        foreach ($this->modules->getModuleNamespaces() as $namespace) {
            $modules = array_diff(scandir(MODULES_DIR . $namespace . '/'), ['.', '..']);
            foreach ($modules as $row) {
                if (in_array(strtolower($row), $coreModules) === false) {
                    $results[$row] = $this->_returnUpdateModuleResult($row);
                }
            }
        }

        ksort($results);

        $this->view->assign('results', $results);

        $this->_clearCaches();
    }

    /**
     * @param $moduleName
     * @return array
     */
    protected function _returnUpdateModuleResult($moduleName)
    {
        $result = $this->_updateModule($moduleName, $this->container);

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

        $serviceId = $module . '.installer';
        if ($this->container->has($serviceId) === true) {
            /** @var \ACP3\Core\Modules\AbstractInstaller $installer */
            $installer = $this->container->get($serviceId);
            if ($installer instanceof \ACP3\Core\Modules\AbstractInstaller &&
                ($this->modules->isInstalled($module) || count($installer->renameModule()) > 0)
            ) {
                $result = $installer->updateSchema();
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
