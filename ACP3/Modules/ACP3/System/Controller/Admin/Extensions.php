<?php

namespace ACP3\Modules\ACP3\System\Controller\Admin;

use ACP3\Core;
use ACP3\Modules\ACP3\Permissions;
use ACP3\Modules\ACP3\System;

/**
 * Class Extensions
 * @package ACP3\Modules\ACP3\System\Controller\Admin
 */
class Extensions extends Core\Modules\AdminController
{
    /**
     * @var \ACP3\Core\XML
     */
    protected $xml;
    /**
     * @var \ACP3\Modules\ACP3\System\Model
     */
    protected $systemModel;
    /**
     * @var \ACP3\Modules\ACP3\System\Helpers
     */
    protected $systemHelpers;
    /**
     * @var \ACP3\Modules\ACP3\Permissions\Cache
     */
    protected $permissionsCache;

    /**
     * @param \ACP3\Core\Modules\Controller\AdminContext $context
     * @param \ACP3\Core\XML                             $xml
     * @param \ACP3\Modules\ACP3\System\Model            $systemModel
     * @param \ACP3\Modules\ACP3\System\Helpers          $systemHelpers
     * @param \ACP3\Modules\ACP3\Permissions\Cache       $permissionsCache
     */
    public function __construct(
        Core\Modules\Controller\AdminContext $context,
        Core\XML $xml,
        System\Model $systemModel,
        System\Helpers $systemHelpers,
        Permissions\Cache $permissionsCache)
    {
        parent::__construct($context);

        $this->xml = $xml;
        $this->systemModel = $systemModel;
        $this->systemHelpers = $systemHelpers;
        $this->permissionsCache = $permissionsCache;
    }

    public function actionDesigns()
    {
        if ($this->request->getParameters()->has('dir')) {
            $this->_designsPost($this->request->getParameters()->get('dir'));
        } else {
            $designs = [];
            $path = ACP3_ROOT_DIR . 'designs/';
            $directories = scandir($path);
            $count_dir = count($directories);
            for ($i = 0; $i < $count_dir; ++$i) {
                $designInfo = $this->xml->parseXmlFile($path . $directories[$i] . '/info.xml', '/design');
                if (!empty($designInfo)) {
                    $designs[$i] = $designInfo;
                    $designs[$i]['selected'] = $this->config->getSettings('system')['design'] === $directories[$i] ? 1 : 0;
                    $designs[$i]['dir'] = $directories[$i];
                }
            }
            $this->view->assign('designs', $designs);
        }
    }

    /**
     * @param $design
     */
    protected function _designsPost($design)
    {
        $bool = false;

        if ((bool)preg_match('=/=', $design) === false &&
            is_file(ACP3_ROOT_DIR . 'designs/' . $design . '/info.xml') === true
        ) {
            $bool = $this->config->setSettings(['design' => $design], 'system');

            // Template Cache leeren
            Core\Cache::purge(CACHE_DIR . 'tpl_compiled');
            Core\Cache::purge(CACHE_DIR . 'tpl_cached');
        }

        $text = $this->lang->t('system', $bool === true ? 'designs_edit_success' : 'designs_edit_error');

        $this->redirectMessages()->setMessage($bool, $text, 'acp/system/extensions/designs');
    }

    public function actionIndex()
    {
        return;
    }

    public function actionModules()
    {
        switch ($this->request->getParameters()->get('action')) {
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
                $this->_renewCaches();

                $modules = $this->modules->getAllModules();
                $installedModules = $newModules = [];

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
        $info = $this->modules->getModuleInfo($this->request->getParameters()->get('dir', ''));
        if (empty($info)) {
            $text = $this->lang->t('system', 'module_not_found');
        } elseif ($info['protected'] === true) {
            $text = $this->lang->t('system', 'mod_deactivate_forbidden');
        } else {
            $serviceId = strtolower($this->request->getParameters()->get('dir', '') . '.installer');

            $container = $this->systemHelpers->updateServiceContainer(true);

            if ($container->has($serviceId) === true) {
                /** @var Core\Modules\AbstractInstaller $installer */
                $installer = $container->get($serviceId);

                // Modulabhängigkeiten prüfen
                $deps = $this->systemHelpers->checkInstallDependencies($installer);

                // Modul installieren
                if (empty($deps)) {
                    $bool = $this->systemModel->update(['active' => 1], ['name' => $this->request->getParameters()->get('dir')]);

                    $this->_renewCaches();
                    Core\Cache::purge(CACHE_DIR . 'sql/container.php');

                    $text = $this->lang->t('system', 'mod_activate_' . ($bool !== false ? 'success' : 'error'));
                } else {
                    $text = sprintf($this->lang->t('system', 'enable_following_modules_first'), implode(', ', $deps));
                }
            } else {
                $text = $this->lang->t('system', 'module_installer_not_found');
            }
        }

        $this->redirectMessages()->setMessage($bool, $text, 'acp/system/extensions/modules');
    }

    protected function _renewCaches()
    {
        $this->get('core.lang.cache')->setLanguageCache($this->lang->getLanguage());
        $this->modules->setModulesCache();
        $this->permissionsCache->setResourcesCache();
    }

    protected function _disableModule()
    {
        $bool = false;
        $info = $this->modules->getModuleInfo($this->request->getParameters()->get('dir', ''));
        if (empty($info)) {
            $text = $this->lang->t('system', 'module_not_found');
        } elseif ($info['protected'] === true) {
            $text = $this->lang->t('system', 'mod_deactivate_forbidden');
        } else {
            $serviceId = strtolower($this->request->getParameters()->get('dir', '') . '.installer');

            if ($this->container->has($serviceId) === true) {
                /** @var Core\Modules\AbstractInstaller $installer */
                $installer = $this->container->get($serviceId);

                // Modulabhängigkeiten prüfen
                $deps = $this->systemHelpers->checkUninstallDependencies($installer::MODULE_NAME, $this->container);

                if (empty($deps)) {
                    $bool = $this->systemModel->update(['active' => 0], ['name' => $this->request->getParameters()->get('dir')]);

                    $this->_renewCaches();
                    Core\Cache::purge(CACHE_DIR . 'tpl_compiled');
                    Core\Cache::purge(CACHE_DIR . 'tpl_cached');
                    Core\Cache::purge(CACHE_DIR . 'sql/container.php');

                    $text = $this->lang->t('system', 'mod_deactivate_' . ($bool !== false ? 'success' : 'error'));
                } else {
                    $text = sprintf($this->lang->t('system', 'module_disable_not_possible'), implode(', ', $deps));
                }
            } else {
                throw new Core\Exceptions\ResultNotExists();
            }
        }

        $this->redirectMessages()->setMessage($bool, $text, 'acp/system/extensions/modules');
    }

    protected function _installModule()
    {
        $bool = false;
        // Nur noch nicht installierte Module berücksichtigen
        if ($this->modules->isInstalled($this->request->getParameters()->get('dir', '')) === false) {
            $serviceId = strtolower($this->request->getParameters()->get('dir', '') . '.installer');

            $container = $this->systemHelpers->updateServiceContainer(true);

            if ($container->has($serviceId) === true) {
                /** @var Core\Modules\AbstractInstaller $installer */
                $installer = $container->get($serviceId);

                // Modulabhängigkeiten prüfen
                $deps = $this->systemHelpers->checkInstallDependencies($installer);

                // Modul installieren
                if (empty($deps)) {
                    $bool = $installer->install();

                    $this->_renewCaches();
                    Core\Cache::purge(CACHE_DIR . 'sql/container.php');

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
        $info = $this->modules->getModuleInfo($this->request->getParameters()->get('dir', ''));
        // Nur installierte und Nicht-Core-Module berücksichtigen
        if ($info['protected'] === false && $this->modules->isInstalled($this->request->getParameters()->get('dir', '')) === true) {
            $serviceId = strtolower($this->request->getParameters()->get('dir', '') . '.installer');

            $container = $this->systemHelpers->updateServiceContainer();

            if ($container->has($serviceId) === true) {
                /** @var Core\Modules\AbstractInstaller $installer */
                $installer = $container->get($serviceId);

                // Modulabhängigkeiten prüfen
                $deps = $this->systemHelpers->checkUninstallDependencies($installer::MODULE_NAME, $container);

                // Modul deinstallieren
                if (empty($deps)) {
                    $bool = $installer->uninstall();

                    $this->_renewCaches();
                    Core\Cache::purge(CACHE_DIR . 'tpl_compiled');
                    Core\Cache::purge(CACHE_DIR . 'tpl_cached');
                    Core\Cache::purge(CACHE_DIR . 'sql/container.php');

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
