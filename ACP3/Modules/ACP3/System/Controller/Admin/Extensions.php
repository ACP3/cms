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
     * @var \ACP3\Modules\ACP3\System\Model\ModuleRepository
     */
    protected $systemModuleRepository;
    /**
     * @var \ACP3\Modules\ACP3\System\Helper\Installer
     */
    protected $installerHelper;
    /**
     * @var \ACP3\Modules\ACP3\Permissions\Cache
     */
    protected $permissionsCache;

    /**
     * @param \ACP3\Core\Modules\Controller\AdminContext       $context
     * @param \ACP3\Core\Modules\ModuleInfoCache               $moduleInfoCache
     * @param \ACP3\Core\XML                                   $xml
     * @param \ACP3\Modules\ACP3\System\Model\ModuleRepository $systemModuleRepository
     * @param \ACP3\Modules\ACP3\System\Helper\Installer       $installerHelper
     * @param \ACP3\Modules\ACP3\Permissions\Cache             $permissionsCache
     */
    public function __construct(
        Core\Modules\Controller\AdminContext $context,
        Core\Modules\ModuleInfoCache $moduleInfoCache,
        Core\XML $xml,
        System\Model\ModuleRepository $systemModuleRepository,
        System\Helper\Installer $installerHelper,
        Permissions\Cache $permissionsCache)
    {
        parent::__construct($context);

        $this->moduleInfoCache = $moduleInfoCache;
        $this->xml = $xml;
        $this->systemModuleRepository = $systemModuleRepository;
        $this->installerHelper = $installerHelper;
        $this->permissionsCache = $permissionsCache;
    }

    /**
     * @param string $dir
     *
     * @return array|\Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function actionDesigns($dir = '')
    {
        if (!empty($dir)) {
            return $this->_designsPost($dir);
        }

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

        return [
            'designs' => $designs
        ];
    }

    /**
     * @param $design
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
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

        $text = $this->translator->t('system', $bool === true ? 'designs_edit_success' : 'designs_edit_error');

        return $this->redirectMessages()->setMessage($bool, $text, $this->request->getFullPath());
    }

    public function actionIndex()
    {
        return;
    }

    /**
     * @param string $action
     * @param string $dir
     *
     * @return array|\Symfony\Component\HttpFoundation\RedirectResponse
     * @throws \ACP3\Core\Exceptions\ResultNotExists
     */
    public function actionModules($action = '', $dir = '')
    {
        switch ($action) {
            case 'activate':
                return $this->_enableModule($dir);
            case 'deactivate':
                return $this->_disableModule($dir);
            case 'install':
                return $this->_installModule($dir);
            case 'uninstall':
                return $this->_uninstallModule($dir);
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

                return [
                    'installed_modules' => $installedModules,
                    'new_modules' => $newModules
                ];
        }
    }

    /**
     * @param string $moduleDirectory
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    protected function _enableModule($moduleDirectory)
    {
        $bool = false;
        $info = $this->modules->getModuleInfo($moduleDirectory);
        if (empty($info)) {
            $text = $this->translator->t('system', 'module_not_found');
        } elseif ($info['protected'] === true) {
            $text = $this->translator->t('system', 'mod_deactivate_forbidden');
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
                    $bool = $this->systemModuleRepository->update(['active' => 1], ['name' => $moduleDirectory]);

                    $this->_renewCaches();
                    Core\Cache::purge(CACHE_DIR . 'sql/container.php');

                    $text = $this->translator->t('system', 'mod_activate_' . ($bool !== false ? 'success' : 'error'));
                } else {
                    $text = $this->translator->t('system', 'enable_following_modules_first',
                        ['%modules%' => implode(', ', $deps)]);
                }
            } else {
                $text = $this->translator->t('system', 'module_installer_not_found');
            }
        }

        return $this->redirectMessages()->setMessage($bool, $text, $this->request->getFullPath());
    }

    protected function _renewCaches()
    {
        $this->get('core.lang.cache')->saveLanguageCache($this->translator->getLanguage());
        $this->moduleInfoCache->saveModulesInfoCache();
        $this->permissionsCache->saveResourcesCache();
    }

    /**
     * @param string $moduleDirectory
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     * @throws \ACP3\Core\Exceptions\ResultNotExists
     */
    protected function _disableModule($moduleDirectory)
    {
        $bool = false;
        $info = $this->modules->getModuleInfo($moduleDirectory);
        if (empty($info)) {
            $text = $this->translator->t('system', 'module_not_found');
        } elseif ($info['protected'] === true) {
            $text = $this->translator->t('system', 'mod_deactivate_forbidden');
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
                    $bool = $this->systemModuleRepository->update(['active' => 0], ['name' => $moduleDirectory]);

                    $this->_renewCaches();
                    Core\Cache::purge(CACHE_DIR . 'tpl_compiled');
                    Core\Cache::purge(CACHE_DIR . 'tpl_cached');
                    Core\Cache::purge(CACHE_DIR . 'sql/container.php');

                    $text = $this->translator->t('system', 'mod_deactivate_' . ($bool !== false ? 'success' : 'error'));
                } else {
                    $text = $this->translator->t('system', 'module_disable_not_possible',
                        ['%modules%' => implode(', ', $deps)]);
                }
            } else {
                throw new Core\Exceptions\ResultNotExists();
            }
        }

        return $this->redirectMessages()->setMessage($bool, $text, $this->request->getFullPath());
    }

    /**
     * @param string $moduleDirectory
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
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

                    $text = $this->translator->t('system',
                        'mod_installation_' . ($bool !== false && $bool2 !== false ? 'success' : 'error'));
                } else {
                    $text = $this->translator->t('system', 'enable_following_modules_first',
                        ['%modules%' => implode(', ', $deps)]);
                }
            } else {
                $text = $this->translator->t('system', 'module_installer_not_found');
            }
        } else {
            $text = $this->translator->t('system', 'module_already_installed');
        }

        return $this->redirectMessages()->setMessage($bool, $text, $this->request->getFullPath());
    }

    /**
     * @param string $moduleDirectory
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
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

                    $text = $this->translator->t('system',
                        'mod_uninstallation_' . ($bool !== false && $bool2 !== false ? 'success' : 'error'));
                } else {
                    $text = $this->translator->t('system', 'uninstall_following_modules_first',
                        ['%modules%' => implode(', ', $deps)]);
                }
            } else {
                $text = $this->translator->t('system', 'module_installer_not_found');
            }
        } else {
            $text = $this->translator->t('system', 'protected_module_description');
        }

        return $this->redirectMessages()->setMessage($bool, $text, $this->request->getFullPath());
    }
}
