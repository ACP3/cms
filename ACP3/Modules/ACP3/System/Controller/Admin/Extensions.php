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
     * @var \ACP3\Core\Modules\ModuleInfoCache
     */
    protected $moduleInfoCache;
    /**
     * @var \ACP3\Core\XML
     */
    protected $xml;
    /**
     * @var \ACP3\Modules\ACP3\System\Model
     */
    protected $systemModel;
    /**
     * @var \ACP3\Modules\ACP3\System\Helper\Installer
     */
    protected $installerHelper;
    /**
     * @var \ACP3\Modules\ACP3\Permissions\Cache
     */
    protected $permissionsCache;

    /**
     * @param \ACP3\Core\Modules\Controller\AdminContext $context
     * @param \ACP3\Core\Modules\ModuleInfoCache         $moduleInfoCache
     * @param \ACP3\Core\XML                             $xml
     * @param \ACP3\Modules\ACP3\System\Model            $systemModel
     * @param \ACP3\Modules\ACP3\System\Helper\Installer $installerHelper
     * @param \ACP3\Modules\ACP3\Permissions\Cache       $permissionsCache
     */
    public function __construct(
        Core\Modules\Controller\AdminContext $context,
        Core\Modules\ModuleInfoCache $moduleInfoCache,
        Core\XML $xml,
        System\Model $systemModel,
        System\Helper\Installer $installerHelper,
        Permissions\Cache $permissionsCache)
    {
        parent::__construct($context);

        $this->moduleInfoCache = $moduleInfoCache;
        $this->xml = $xml;
        $this->systemModel = $systemModel;
        $this->installerHelper = $installerHelper;
        $this->permissionsCache = $permissionsCache;
    }

    /**
     * @param string $dir
     */
    public function actionDesigns($dir = '')
    {
        if (!empty($dir)) {
            $this->_designsPost($dir);
        } else {
            $designs = [];
            $path = ACP3_ROOT_DIR . 'designs/';
            $directories = Core\Filesystem::scandir($path);
            $countDir = count($directories);
            for ($i = 0; $i < $countDir; ++$i) {
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

        $this->redirectMessages()->setMessage($bool, $text, $this->request->getFullPath());
    }

    public function actionIndex()
    {
        return;
    }

    /**
     * @param string $action
     * @param string $dir
     *
     * @throws \ACP3\Core\Exceptions\ResultNotExists
     */
    public function actionModules($action = '', $dir = '')
    {
        switch ($action) {
            case 'activate':
                $this->_enableModule($dir);
                break;
            case 'deactivate':
                $this->_disableModule($dir);
                break;
            case 'install':
                $this->_installModule($dir);
                break;
            case 'uninstall':
                $this->_uninstallModule($dir);
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

    /**
     * @param string $moduleDirectory
     */
    protected function _enableModule($moduleDirectory)
    {
        $bool = false;
        $info = $this->modules->getModuleInfo($moduleDirectory);
        if (empty($info)) {
            $text = $this->lang->t('system', 'module_not_found');
        } elseif ($info['protected'] === true) {
            $text = $this->lang->t('system', 'mod_deactivate_forbidden');
        } else {
            $serviceId = strtolower($moduleDirectory . '.installer.schema');

            $container = $this->installerHelper->updateServiceContainer(true);

            if ($container->has($serviceId) === true) {
                /** @var Core\Modules\Installer\SchemaInterface $moduleSchema */
                $moduleSchema = $container->get($serviceId);

                // Modulabhängigkeiten prüfen
                $deps = $this->installerHelper->checkInstallDependencies($moduleSchema);

                // Modul installieren
                if (empty($deps)) {
                    $bool = $this->systemModel->update(['active' => 1], ['name' => $moduleDirectory]);

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

        $this->redirectMessages()->setMessage($bool, $text, $this->request->getFullPath());
    }

    protected function _renewCaches()
    {
        $this->get('core.lang.cache')->saveLanguageCache($this->lang->getLanguage());
        $this->moduleInfoCache->saveModulesInfoCache();
        $this->permissionsCache->saveResourcesCache();
    }

    /**
     * @param string $moduleDirectory
     *
     * @throws \ACP3\Core\Exceptions\ResultNotExists
     */
    protected function _disableModule($moduleDirectory)
    {
        $bool = false;
        $info = $this->modules->getModuleInfo($moduleDirectory);
        if (empty($info)) {
            $text = $this->lang->t('system', 'module_not_found');
        } elseif ($info['protected'] === true) {
            $text = $this->lang->t('system', 'mod_deactivate_forbidden');
        } else {
            $serviceId = strtolower($moduleDirectory . '.installer.schema');

            if ($this->container->has($serviceId) === true) {
                /** @var Core\Modules\Installer\SchemaInterface $moduleSchema */
                $moduleSchema = $this->container->get($serviceId);

                // Modulabhängigkeiten prüfen
                $deps = $this->installerHelper->checkUninstallDependencies(
                    $moduleSchema->getModuleName(),
                    $this->container
                );

                if (empty($deps)) {
                    $bool = $this->systemModel->update(['active' => 0], ['name' => $moduleDirectory]);

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

        $this->redirectMessages()->setMessage($bool, $text, $this->request->getFullPath());
    }

    /**
     * @param string $moduleDirectory
     */
    protected function _installModule($moduleDirectory)
    {
        $bool = false;
        // Nur noch nicht installierte Module berücksichtigen
        if ($this->modules->isInstalled($moduleDirectory) === false) {
            $serviceId = strtolower($moduleDirectory . '.installer.schema');

            $container = $this->installerHelper->updateServiceContainer(true);

            if ($container->has($serviceId) === true) {
                /** @var Core\Modules\Installer\SchemaInterface $moduleSchema */
                $moduleSchema = $container->get($serviceId);

                // Modulabhängigkeiten prüfen
                $deps = $this->installerHelper->checkInstallDependencies($moduleSchema);

                // Modul installieren
                if (empty($deps)) {
                    $bool = $this->container->get('core.modules.schemaInstaller')->install($moduleSchema);
                    $bool2 = $this->container->get('core.modules.aclInstaller')->install($moduleSchema);

                    $this->_renewCaches();
                    Core\Cache::purge(CACHE_DIR . 'sql/container.php');

                    $text = $this->lang->t('system', 'mod_installation_' . ($bool !== false && $bool2 !== false ? 'success' : 'error'));
                } else {
                    $text = sprintf($this->lang->t('system', 'enable_following_modules_first'), implode(', ', $deps));
                }
            } else {
                $text = $this->lang->t('system', 'module_installer_not_found');
            }
        } else {
            $text = $this->lang->t('system', 'module_already_installed');
        }

        $this->redirectMessages()->setMessage($bool, $text, $this->request->getFullPath());
    }

    /**
     * @param string $moduleDirectory
     */
    protected function _uninstallModule($moduleDirectory)
    {
        $bool = false;
        $info = $this->modules->getModuleInfo($moduleDirectory);
        // Nur installierte und Nicht-Core-Module berücksichtigen
        if ($info['protected'] === false && $this->modules->isInstalled($moduleDirectory) === true) {
            $serviceId = strtolower($moduleDirectory . '.installer.schema');

            $container = $this->installerHelper->updateServiceContainer();

            if ($container->has($serviceId) === true) {
                /** @var Core\Modules\Installer\SchemaInterface $moduleSchema */
                $moduleSchema = $container->get($serviceId);

                // Modulabhängigkeiten prüfen
                $deps = $this->installerHelper->checkUninstallDependencies(
                    $moduleSchema->getModuleName(),
                    $container
                );

                // Modul deinstallieren
                if (empty($deps)) {
                    $bool = $this->container->get('core.modules.schemaInstaller')->uninstall($moduleSchema);
                    $bool2 = $this->container->get('core.modules.aclInstaller')->uninstall($moduleSchema);

                    $this->_renewCaches();
                    Core\Cache::purge(CACHE_DIR . 'tpl_compiled');
                    Core\Cache::purge(CACHE_DIR . 'tpl_cached');
                    Core\Cache::purge(CACHE_DIR . 'sql/container.php');

                    $text = $this->lang->t('system', 'mod_uninstallation_' . ($bool !== false && $bool2 !== false ? 'success' : 'error'));
                } else {
                    $text = sprintf($this->lang->t('system', 'uninstall_following_modules_first'), implode(', ', $deps));
                }
            } else {
                $text = $this->lang->t('system', 'module_installer_not_found');
            }
        } else {
            $text = $this->lang->t('system', 'protected_module_description');
        }

        $this->redirectMessages()->setMessage($bool, $text, $this->request->getFullPath());
    }
}
