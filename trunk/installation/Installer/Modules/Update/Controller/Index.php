<?php

namespace ACP3\Installer\Modules\Update\Controller;

use ACP3\Core\Cache;
use ACP3\Installer\Core;
use ACP3\Installer\Modules\Update\Helpers;

/**
 * Class Index
 * @package ACP3\Installer\Modules\Update\Controller
 */
class Index extends Core\Modules\Controller
{
    /**
     * @var \ACP3\Installer\Modules\Update\Helpers
     */
    protected $updateHelper;

    public function __construct(
        Core\Context $context,
        Helpers $updateHelper
    )
    {
        parent::__construct($context);

        $this->updateHelper = $updateHelper;
    }

    public function actionIndex()
    {
        if (empty($_POST) === false) {
            $results = array();
            // Zuerst die wichtigen System-Module aktualisieren...
            $coreModules = array('system', 'permissions', 'users');
            foreach ($coreModules as $row) {
                $results[$row] = $this->_updateModule($row);
            }

            // ...danach die Restlichen
            $modules = array_diff(scandir(MODULES_DIR), array('.', '..'));
            foreach ($modules as $row) {
                if (in_array(strtolower($row), $coreModules) === false) {
                    $results[$row] = $this->_updateModule($row);
                }
            }

            ksort($results);

            $this->view->assign('results', $results);

            // Cache leeren
            Cache::purge(UPLOADS_DIR . 'cache/minify');
            Cache::purge(UPLOADS_DIR . 'cache/sql');
            Cache::purge(UPLOADS_DIR . 'cache/tpl_compiled');
        }
    }

    /**
     * @param $moduleName
     *
     * @return array
     */
    protected function _updateModule($moduleName)
    {
        $result = $this->updateHelper->updateModule($moduleName, $this->container);

        return array(
            'text' => sprintf($this->lang->t('update', 'db_update_text'), ucfirst($moduleName)),
            'class' => $result === 1 ? 'success' : ($result === 0 ? 'danger' : 'info'),
            'result_text' => $this->lang->t('update', $result === 1 ? 'db_update_success' : ($result === 0 ? 'db_update_error' : 'db_update_no_update'))
        );
    }

}