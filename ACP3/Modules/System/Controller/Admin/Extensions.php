<?php

namespace ACP3\Modules\System\Controller\Admin;

use ACP3\Core;
use ACP3\Modules\Permissions;
use ACP3\Modules\System;

/**
 * Class Extensions
 * @package ACP3\Modules\System\Controller\Admin
 */
class Extensions extends Core\Modules\Controller\Admin
{

    /**
     * @var System\Model
     */
    protected $systemModel;
    /**
     * @var Core\Config
     */
    protected $systemConfig;
    /**
     * @var \ACP3\Modules\Permissions\Cache
     */
    protected $permissionsCache;

    public function __construct(
        Core\Context\Admin $context,
        \Doctrine\DBAL\Connection $db,
        System\Model $systemModel,
        Core\Config $systemConfig,
        Permissions\Cache $permissionsCache)
    {
        parent::__construct($context);

        $this->systemModel = $systemModel;
        $this->systemConfig = $systemConfig;
        $this->permissionsCache = $permissionsCache;
    }

    public function actionDesigns()
    {
        $redirect = $this->redirectMessages();

        if (isset($this->request->dir)) {
            $bool = false;

            if ((bool)preg_match('=/=', $this->request->dir) === false &&
                is_file(ACP3_ROOT_DIR . 'designs/' . $this->request->dir . '/info.xml') === true
            ) {
                $bool = $this->systemConfig->setSettings(array('design' => $this->request->dir));

                // Template Cache leeren
                Core\Cache::purge(UPLOADS_DIR . 'cache/tpl_compiled');
                Core\Cache::purge(UPLOADS_DIR . 'cache/tpl_cached');
            }
            $text = $this->lang->t('system', $bool === true ? 'designs_edit_success' : 'designs_edit_error');

            $redirect->setMessage($bool, $text, 'acp/system/index/designs');
        } else {
            $redirect->getMessage();

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
        switch ($this->request->action) {
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
                $this->redirectMessages()->getMessage();

                $this->_renewCaches();

                $modules = $this->modules->getAllModules();
                $installedModules = $newModules = array();

                foreach ($modules as $key => $values) {
                    $values['dir'] = strtolower($values['dir']);
                    if ($this->modules->isInstalled($values['dir']) === true) {
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
        $info = $this->modules->getModuleInfo($this->request->dir);
        if (empty($info)) {
            $text = $this->lang->t('system', 'module_not_found');
        } elseif ($info['protected'] === true) {
            $text = $this->lang->t('system', 'mod_deactivate_forbidden');
        } else {
            $bool = $this->systemModel->update(array('active' => 1), array('name' => $this->request->dir));

            $this->_renewCaches();

            $text = $this->lang->t('system', 'mod_activate_' . ($bool !== false ? 'success' : 'error'));
        }

        $this->redirectMessages()->setMessage($bool, $text, 'acp/system/extensions/modules');
    }

    protected function _renewCaches()
    {
        $this->lang->setLanguageCache();
        $this->modules->setModulesCache();

        $this->permissionsCache->setResourcesCache();
    }

    protected function _disableModule()
    {
        $bool = false;
        $info = $this->modules->getModuleInfo($this->request->dir);
        if (empty($info)) {
            $text = $this->lang->t('system', 'module_not_found');
        } elseif ($info['protected'] === true) {
            $text = $this->lang->t('system', 'mod_deactivate_forbidden');
        } else {
            // Modulabhängigkeiten prüfen
            $deps = $this->get('system.helpers')->checkUninstallDependencies($this->request->dir);

            if (empty($deps)) {
                $bool = $this->systemModel->update(array('active' => 0), array('name' => $this->request->dir));

                $this->_renewCaches();

                $text = $this->lang->t('system', 'mod_deactivate_' . ($bool !== false ? 'success' : 'error'));
            } else {
                $text = sprintf($this->lang->t('system', 'module_disable_not_possible'), implode(', ', $deps));
            }
        }

        $this->redirectMessages()->setMessage($bool, $text, 'acp/system/extensions/modules');
    }

    protected function _installModule()
    {
        $bool = false;
        // Nur noch nicht installierte Module berücksichtigen
        if ($this->modules->isInstalled($this->request->dir) === false) {
            $serviceId = strtolower($this->request->dir . '.installer');

            if ($this->container->has($serviceId) === true) {
                // Modulabhängigkeiten prüfen
                $deps = $this->get('system.helpers')->checkInstallDependencies($this->request->dir);

                // Modul installieren
                if (empty($deps)) {
                    $bool = $this->get($serviceId)->install();

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

        $this->redirectMessages()->setMessage($bool, $text, 'acp/system/extensions/modules');
    }

    protected function _uninstallModule()
    {
        $bool = false;
        $info = $this->modules->getModuleInfo($this->request->dir);
        // Nur installierte und Nicht-Core-Module berücksichtigen
        if ($info['protected'] === false && $this->modules->isInstalled($this->request->dir) === true) {
            $serviceId = strtolower($this->request->dir . '.installer');

            if ($this->container->has($serviceId) === true) {
                // Modulabhängigkeiten prüfen
                $deps = $this->get('system.helpers')->checkUninstallDependencies($this->request->dir);

                // Modul deinstallieren
                if (empty($deps)) {
                    $bool = $this->get($serviceId)->uninstall();

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

        $this->redirectMessages()->setMessage($bool, $text, 'acp/system/extensions/modules');
    }

}