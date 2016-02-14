<?php
/**
 * Copyright (c) 2016 by the ACP3 Developers. See the LICENCE file at the top-level module directory for licencing details.
 */

namespace ACP3\Modules\ACP3\System\Controller\Admin\Extensions;

use ACP3\Core;
use ACP3\Modules\ACP3\Permissions;
use ACP3\Modules\ACP3\System;

/**
 * Class Modules
 * @package ACP3\Modules\ACP3\System\Controller\Admin\Extensions
 */
class Modules extends Core\Modules\AdminController
{
    /**
     * @var \ACP3\Core\Modules\ModuleInfoCache
     */
    protected $moduleInfoCache;
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
     * Modules constructor.
     *
     * @param \ACP3\Core\Modules\Controller\AdminContext       $context
     * @param \ACP3\Core\Modules\ModuleInfoCache               $moduleInfoCache
     * @param \ACP3\Modules\ACP3\System\Model\ModuleRepository $systemModuleRepository
     * @param \ACP3\Modules\ACP3\System\Helper\Installer       $installerHelper
     * @param \ACP3\Modules\ACP3\Permissions\Cache             $permissionsCache
     */
    public function __construct(
        Core\Modules\Controller\AdminContext $context,
        Core\Modules\ModuleInfoCache $moduleInfoCache,
        System\Model\ModuleRepository $systemModuleRepository,
        System\Helper\Installer $installerHelper,
        Permissions\Cache $permissionsCache)
    {
        parent::__construct($context);

        $this->moduleInfoCache = $moduleInfoCache;
        $this->systemModuleRepository = $systemModuleRepository;
        $this->installerHelper = $installerHelper;
        $this->permissionsCache = $permissionsCache;
    }

    /**
     * @param string $action
     * @param string $dir
     *
     * @return array|\Symfony\Component\HttpFoundation\RedirectResponse
     * @throws \ACP3\Core\Exceptions\ResultNotExists
     */
    public function execute($action = '', $dir = '')
    {
        switch ($action) {
            case 'activate':
                return $this->enableModule($dir);
            case 'deactivate':
                return $this->disableModule($dir);
            case 'install':
                return $this->installModule($dir);
            case 'uninstall':
                return $this->uninstallModule($dir);
            default:
                $this->renewCaches();

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
    protected function enableModule($moduleDirectory)
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

                    $this->renewCaches();
                    Core\Cache::purge($this->appPath->getCacheDir() . 'sql/container.php');

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

    protected function renewCaches()
    {
        $this->get('core.lang.cache')->saveLanguageCache($this->translator->getLocale());
        $this->moduleInfoCache->saveModulesInfoCache();
        $this->permissionsCache->saveResourcesCache();
    }

    /**
     * @param string $moduleDirectory
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     * @throws \ACP3\Core\Exceptions\ResultNotExists
     */
    protected function disableModule($moduleDirectory)
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

                    $this->renewCaches();
                    Core\Cache::purge($this->appPath->getCacheDir() . 'tpl_compiled');
                    Core\Cache::purge($this->appPath->getCacheDir() . 'tpl_cached');
                    Core\Cache::purge($this->appPath->getCacheDir() . 'sql/container.php');

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
    protected function installModule($moduleDirectory)
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

                    $this->renewCaches();
                    Core\Cache::purge($this->appPath->getCacheDir() . 'sql/container.php');

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
    protected function uninstallModule($moduleDirectory)
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

                    $this->renewCaches();
                    Core\Cache::purge($this->appPath->getCacheDir() . 'tpl_compiled');
                    Core\Cache::purge($this->appPath->getCacheDir() . 'tpl_cached');
                    Core\Cache::purge($this->appPath->getCacheDir() . 'sql/container.php');

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
