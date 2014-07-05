<?php

namespace ACP3\Modules\System\Controller\Admin;

use ACP3\Core;
use ACP3\Modules\System;

/**
 * Description of SystemAdmin
 *
 * @author Tino Goratsch
 */
class Extensions extends Core\Modules\Controller\Admin
{

    /**
     * @var System\Model
     */
    private $model;

    public function preDispatch()
    {
        parent::preDispatch();

        $this->model = new System\Model($this->db);
    }

    public function actionDesigns()
    {
        if (isset($this->uri->dir)) {
            $bool = false;

            if ((bool)preg_match('=/=', $this->uri->dir) === false &&
                is_file(ACP3_ROOT_DIR . 'designs/' . $this->uri->dir . '/info.xml') === true
            ) {
                $config = new Core\Config($this->db, 'system');
                $bool = $config->setSettings(array('design' => $this->uri->dir));

                // Template Cache leeren
                Core\Cache::purge('tpl_compiled');
                Core\Cache::purge('tpl_cached');
            }
            $text = $this->lang->t('system', $bool === true ? 'designs_edit_success' : 'designs_edit_error');

            Core\Functions::setRedirectMessage($bool, $text, 'acp/system/index/designs');
        } else {
            Core\Functions::getRedirectMessage();

            $designs = array();
            $path = ACP3_ROOT_DIR . 'designs/';
            $directories = scandir($path);
            $count_dir = count($directories);
            for ($i = 0; $i < $count_dir; ++$i) {
                $design_info = Core\XML::parseXmlFile($path . $directories[$i] . '/info.xml', '/design');
                if (!empty($design_info)) {
                    $designs[$i] = $design_info;
                    $designs[$i]['selected'] = CONFIG_DESIGN === $directories[$i] ? 1 : 0;
                    $designs[$i]['dir'] = $directories[$i];
                }
            }
            $this->view->assign('designs', $designs);
        }
    }

    public function actionIndex()
    {
        return;
    }

    public function actionModules()
    {
        switch ($this->uri->action) {
            case 'activate':
                $this->_enableModule();
                break;
            case 'deactivate':
                $this->_disableModule();
                break;
            case 'install':
                $this->_installModule();
                break;
            case 'uninstall':
                $this->_uninstallModule();
                break;
            default:
                Core\Functions::getRedirectMessage();

                $this->_renewCaches();

                $modules = Core\Modules::getAllModules();
                $installedModules = $newModules = array();

                foreach ($modules as $key => $values) {
                    $values['dir'] = strtolower($values['dir']);
                    if (Core\Modules::isInstalled($values['dir']) === true) {
                        $installedModules[$key] = $values;
                    } else {
                        $newModules[$key] = $values;
                    }
                }

                $this->view->assign('installed_modules', $installedModules);
                $this->view->assign('new_modules', $newModules);
        }
    }

    protected function _enableModule()
    {
        $bool = false;
        $info = Core\Modules::getModuleInfo($this->uri->dir);
        if (empty($info)) {
            $text = $this->lang->t('system', 'module_not_found');
        } elseif ($info['protected'] === true) {
            $text = $this->lang->t('system', 'mod_deactivate_forbidden');
        } else {
            $bool = $this->model->update(array('active' => 1), array('name' => $this->uri->dir));

            $this->_renewCaches();

            $text = $this->lang->t('system', 'mod_activate_' . ($bool !== false ? 'success' : 'error'));
        }
        Core\Functions::setRedirectMessage($bool, $text, 'acp/system/extensions/modules');
    }

    protected function _disableModule()
    {
        $bool = false;
        $info = Core\Modules::getModuleInfo($this->uri->dir);
        if (empty($info)) {
            $text = $this->lang->t('system', 'module_not_found');
        } elseif ($info['protected'] === true) {
            $text = $this->lang->t('system', 'mod_deactivate_forbidden');
        } else {
            // Modulabhängigkeiten prüfen
            $deps = System\Helpers::checkUninstallDependencies($this->uri->dir);

            if (empty($deps)) {
                $bool = $this->model->update(array('active' => 0), array('name' => $this->uri->dir));

                $this->_renewCaches();

                $text = $this->lang->t('system', 'mod_deactivate_' . ($bool !== false ? 'success' : 'error'));
            } else {
                $text = sprintf($this->lang->t('system', 'module_disable_not_possible'), implode(', ', $deps));
            }
        }
        Core\Functions::setRedirectMessage($bool, $text, 'acp/system/extensions/modules');
    }

    protected function _installModule()
    {
        $bool = false;
        // Nur noch nicht installierte Module berücksichtigen
        if (Core\Modules::isInstalled($this->uri->dir) === false) {
            $moduleName = ucfirst($this->uri->dir);
            $path = MODULES_DIR . $moduleName . '/Installer.php';
            if (is_file($path) === true) {
                // Modulabhängigkeiten prüfen
                $deps = System\Helpers::checkInstallDependencies($this->uri->dir);

                // Modul installieren
                if (empty($deps)) {
                    $className = Core\Modules\AbstractInstaller::buildClassName($this->uri->dir);
                    /** @var Core\Modules\AbstractInstaller $installer */
                    $installer = new $className($this->db);
                    $bool = $installer->install();

                    $this->_renewCaches();

                    $text = $this->lang->t('system', 'mod_installation_' . ($bool !== false ? 'success' : 'error'));
                } else {
                    $text = sprintf($this->lang->t('system', 'enable_following_modules_first'), implode(', ', $deps));
                }
            } else {
                $text = $this->lang->t('system', 'module_installer_not_found');
            }
        } else {
            $text = $this->lang->t('system', 'module_already_installed');
        }
        Core\Functions::setRedirectMessage($bool, $text, 'acp/system/extensions/modules');
    }

    protected function _uninstallModule()
    {
        $bool = false;
        $info = Core\Modules::getModuleInfo($this->uri->dir);
        // Nur installierte und Nicht-Core-Module berücksichtigen
        if ($info['protected'] === false && Core\Modules::isInstalled($this->uri->dir) === true) {
            $moduleName = ucfirst($this->uri->dir);
            $path = MODULES_DIR . $moduleName . '/Installer.php';
            if (is_file($path) === true) {
                // Modulabhängigkeiten prüfen
                $deps = System\Helpers::checkUninstallDependencies($this->uri->dir);

                // Modul deinstallieren
                if (empty($deps)) {
                    $className = Core\Modules\AbstractInstaller::buildClassName($this->uri->dir);
                    /** @var Core\Modules\AbstractInstaller $installer */
                    $installer = new $className($this->db);
                    $bool = $installer->uninstall();

                    $this->_renewCaches();

                    $text = $this->lang->t('system', 'mod_uninstallation_' . ($bool !== false ? 'success' : 'error'));
                } else {
                    $text = sprintf($this->lang->t('system', 'uninstall_following_modules_first'), implode(', ', $deps));
                }
            } else {
                $text = $this->lang->t('system', 'module_installer_not_found');
            }
        } else {
            $text = $this->lang->t('system', 'protected_module_description');
        }
        Core\Functions::setRedirectMessage($bool, $text, 'acp/system/extensions/modules');
    }

    protected function _renewCaches()
    {
        $this->lang->setLanguageCache();
        Core\Modules::setModulesCache();
        $this->get('ACL')->setResourcesCache();
    }

}