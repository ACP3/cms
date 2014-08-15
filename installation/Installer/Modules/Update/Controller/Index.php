<?php

namespace ACP3\Installer\Modules\Update\Controller;

use ACP3\Core\Cache2;
use ACP3\Core\Registry;
use ACP3\Core\Cache;
use ACP3\Installer\Core;

/**
 * Class Index
 * @package ACP3\Installer\Modules\Update\Controller
 */
class Index extends Core\Modules\Controller
{

    public function actionIndex()
    {
        if (isset($_POST['update'])) {
            $results = array();
            // Zuerst die wichtigen System-Module aktualisieren...
            $coreModules = array('system', 'permissions', 'users');
            foreach ($coreModules as $row) {
                $result = Core\Functions::updateModule($row);
                $module = ucfirst($row);
                $results[$module] = array(
                    'text' => sprintf($this->lang->t('db_update_text'), $module),
                    'class' => $result === 1 ? 'success' : ($result === 0 ? 'danger' : 'info'),
                    'result_text' => $this->lang->t($result === 1 ? 'db_update_success' : ($result === 0 ? 'db_update_error' : 'db_update_no_update'))
                );
            }

            // ...danach die Restlichen
            $modules = array_diff(scandir(MODULES_DIR), array('.', '..'));
            foreach ($modules as $row) {
                if (in_array(strtolower($row), $coreModules) === false) {
                    $result = Core\Functions::updateModule($row);
                    $module = ucfirst($row);
                    $results[$module] = array(
                        'text' => sprintf($this->lang->t('db_update_text'), $module),
                        'class' => $result === 1 ? 'success' : ($result === 0 ? 'danger' : 'info'),
                        'result_text' => $this->lang->t($result === 1 ? 'db_update_success' : ($result === 0 ? 'db_update_error' : 'db_update_no_update'))
                    );
                }
            }

            ksort($results);

            $this->view->assign('results', $results);

            // Cache leeren
            Cache2::purge('minify');
            Cache2::purge('sql');
            Cache2::purge('tpl_compiled');
        }
    }

}