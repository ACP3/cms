<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\System\Controller\Admin\Extensions;

use ACP3\Core;
use ACP3\Core\Helpers\RedirectMessages;
use ACP3\Modules\ACP3\Permissions;
use ACP3\Modules\ACP3\System;
use Psr\Container\ContainerInterface;

class Modules extends Core\Controller\AbstractWidgetAction
{
    /**
     * @var \ACP3\Core\Modules\ModuleInfoCacheInterface
     */
    private $moduleInfoCache;
    /**
     * @var \ACP3\Modules\ACP3\System\Model\Repository\ModulesRepository
     */
    private $systemModuleRepository;
    /**
     * @var \ACP3\Modules\ACP3\System\Helper\Installer
     */
    private $installerHelper;
    /**
     * @var \ACP3\Modules\ACP3\Permissions\Cache
     */
    private $permissionsCache;
    /**
     * @var \ACP3\Core\I18n\DictionaryCacheInterface
     */
    private $dictionaryCache;
    /**
     * @var \Psr\Container\ContainerInterface
     */
    private $schemaLocator;
    /**
     * @var Core\Modules\SchemaInstaller
     */
    private $schemaInstaller;
    /**
     * @var Core\Modules\AclInstaller
     */
    private $aclInstaller;
    /**
     * @var \ACP3\Core\Modules
     */
    private $modules;
    /**
     * @var \ACP3\Modules\ACP3\System\ViewProviders\AdminModulesViewProvider
     */
    private $adminModulesViewProvider;
    /**
     * @var \ACP3\Core\Helpers\RedirectMessages
     */
    private $redirectMessages;
    /**
     * @var \ACP3\Modules\ACP3\System\Services\CacheClearService
     */
    private $cacheClearService;

    public function __construct(
        Core\Controller\Context\WidgetContext $context,
        RedirectMessages $redirectMessages,
        Core\Modules $modules,
        Core\I18n\DictionaryCacheInterface $dictionaryCache,
        Core\Modules\ModuleInfoCacheInterface $moduleInfoCache,
        System\Model\Repository\ModulesRepository $systemModuleRepository,
        System\Helper\Installer $installerHelper,
        Permissions\Cache $permissionsCache,
        ContainerInterface $schemaLocator,
        Core\Modules\SchemaInstaller $schemaInstaller,
        Core\Modules\AclInstaller $aclInstaller,
        System\ViewProviders\AdminModulesViewProvider $adminModulesViewProvider,
        System\Services\CacheClearService $cacheClearService
    ) {
        parent::__construct($context);

        $this->moduleInfoCache = $moduleInfoCache;
        $this->systemModuleRepository = $systemModuleRepository;
        $this->installerHelper = $installerHelper;
        $this->permissionsCache = $permissionsCache;
        $this->dictionaryCache = $dictionaryCache;
        $this->schemaLocator = $schemaLocator;
        $this->schemaInstaller = $schemaInstaller;
        $this->aclInstaller = $aclInstaller;
        $this->modules = $modules;
        $this->adminModulesViewProvider = $adminModulesViewProvider;
        $this->redirectMessages = $redirectMessages;
        $this->cacheClearService = $cacheClearService;
    }

    /**
     * @return array|\Symfony\Component\HttpFoundation\RedirectResponse
     *
     * @throws \Doctrine\DBAL\Exception
     */
    public function execute(?string $action = null, ?string $dir = null)
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
                return $this->outputPage();
        }
    }

    /**
     * @return \Symfony\Component\HttpFoundation\JsonResponse|\Symfony\Component\HttpFoundation\RedirectResponse
     *
     * @throws \Doctrine\DBAL\Exception
     */
    private function enableModule(string $moduleDirectory)
    {
        $result = false;

        try {
            $this->checkPreconditions($moduleDirectory);

            $this->moduleInstallerExists($moduleDirectory);

            $moduleSchema = $this->schemaLocator->get($moduleDirectory);

            $dependencies = $this->installerHelper->checkInstallDependencies($moduleSchema);
            $this->checkForFailedModuleDependencies($dependencies, 'enable_following_modules_first');

            $result = $this->saveModuleState($moduleDirectory, 1);

            $this->purgeCaches();

            $text = $this->translator->t(
                'system',
                'mod_activate_' . ($result !== false ? 'success' : 'error')
            );
        } catch (System\Exception\ModuleInstallerException $e) {
            $text = $e->getMessage();
        }

        return $this->redirectMessages->setMessage($result, $text, $this->request->getFullPath());
    }

    /**
     * @throws \ACP3\Modules\ACP3\System\Exception\ModuleInstallerException
     */
    private function checkPreconditions(string $moduleDirectory): void
    {
        $info = $this->modules->getModuleInfo($moduleDirectory);
        if (empty($info) || $info['protected'] === true || $info['installable'] === false) {
            throw new System\Exception\ModuleInstallerException($this->translator->t('system', 'could_not_complete_request'));
        }
    }

    /**
     * @throws System\Exception\ModuleInstallerException
     */
    private function moduleInstallerExists(string $serviceId): void
    {
        if ($this->schemaLocator->has($serviceId) === false) {
            throw new System\Exception\ModuleInstallerException($this->translator->t('system', 'module_installer_not_found'));
        }
    }

    /**
     * @throws \ACP3\Modules\ACP3\System\Exception\ModuleInstallerException
     */
    private function checkForFailedModuleDependencies(array $dependencies, string $phrase): void
    {
        if (!empty($dependencies)) {
            throw new System\Exception\ModuleInstallerException($this->translator->t('system', $phrase, ['%modules%' => implode(', ', $dependencies)]));
        }
    }

    /**
     * @return bool|int
     *
     * @throws \Doctrine\DBAL\Exception
     */
    private function saveModuleState(string $moduleDirectory, int $active)
    {
        return $this->systemModuleRepository->update(['active' => $active], ['name' => $moduleDirectory]);
    }

    private function purgeCaches(): void
    {
        Core\Cache\Purge::doPurge([
            $this->appPath->getCacheDir() . 'container.php',
            $this->appPath->getCacheDir() . 'container.php.meta',
        ]);

        $this->cacheClearService->clearCacheByType('general');
        $this->cacheClearService->clearCacheByType('page');
        $this->cacheClearService->clearCacheByType('templates');
    }

    /**
     * @throws \Doctrine\DBAL\Exception
     */
    private function renewCaches(): void
    {
        $this->dictionaryCache->saveLanguageCache($this->translator->getLocale());
        $this->moduleInfoCache->saveModulesInfoCache();
        $this->permissionsCache->saveResourcesCache();
    }

    /**
     * @return \Symfony\Component\HttpFoundation\JsonResponse|\Symfony\Component\HttpFoundation\RedirectResponse
     *
     * @throws \Doctrine\DBAL\Exception
     */
    private function disableModule(string $moduleDirectory)
    {
        $result = false;

        try {
            $this->checkPreconditions($moduleDirectory);

            $this->moduleInstallerExists($moduleDirectory);

            $moduleSchema = $this->schemaLocator->get($moduleDirectory);

            $dependencies = $this->installerHelper->checkUninstallDependencies($moduleSchema);
            $this->checkForFailedModuleDependencies($dependencies, 'module_disable_not_possible');

            $result = $this->saveModuleState($moduleDirectory, 0);

            $this->purgeCaches();

            $text = $this->translator->t(
                'system',
                'mod_deactivate_' . ($result !== false ? 'success' : 'error')
            );
        } catch (System\Exception\ModuleInstallerException $e) {
            $text = $e->getMessage();
        }

        return $this->redirectMessages->setMessage($result, $text, $this->request->getFullPath());
    }

    /**
     * @return \Symfony\Component\HttpFoundation\JsonResponse|\Symfony\Component\HttpFoundation\RedirectResponse
     *
     * @throws \Doctrine\DBAL\Exception
     */
    private function installModule(string $moduleDirectory)
    {
        $result = false;

        try {
            if ($this->modules->isInstalled($moduleDirectory) === true) {
                throw new System\Exception\ModuleInstallerException($this->translator->t('system', 'module_already_installed'));
            }

            $this->moduleInstallerExists($moduleDirectory);

            $moduleSchema = $this->schemaLocator->get($moduleDirectory);

            $dependencies = $this->installerHelper->checkInstallDependencies($moduleSchema);
            $this->checkForFailedModuleDependencies($dependencies, 'enable_following_modules_first');

            $result = $this->schemaInstaller->install($moduleSchema);
            $resultAcl = $this->aclInstaller->install($moduleSchema);

            $this->purgeCaches();

            $text = $this->translator->t(
                'system',
                'mod_installation_' . ($result !== false && $resultAcl !== false ? 'success' : 'error')
            );
        } catch (System\Exception\ModuleInstallerException $e) {
            $text = $e->getMessage();
        }

        return $this->redirectMessages->setMessage($result, $text, $this->request->getFullPath());
    }

    /**
     * @return \Symfony\Component\HttpFoundation\JsonResponse|\Symfony\Component\HttpFoundation\RedirectResponse
     */
    private function uninstallModule(string $moduleDirectory)
    {
        $result = false;

        try {
            $info = $this->modules->getModuleInfo($moduleDirectory);
            if ($info['protected'] === true || $this->modules->isInstalled($moduleDirectory) === false) {
                throw new System\Exception\ModuleInstallerException($this->translator->t('system', 'protected_module_description'));
            }

            $this->moduleInstallerExists($moduleDirectory);

            $moduleSchema = $this->schemaLocator->get($moduleDirectory);

            $dependencies = $this->installerHelper->checkUninstallDependencies($moduleSchema);
            $this->checkForFailedModuleDependencies($dependencies, 'uninstall_following_modules_first');

            $result = $this->schemaInstaller->uninstall($moduleSchema);
            $resultAcl = $this->aclInstaller->uninstall($moduleSchema);

            $this->purgeCaches();

            $text = $this->translator->t(
                'system',
                'mod_uninstallation_' . ($result !== false && $resultAcl !== false ? 'success' : 'error')
            );
        } catch (System\Exception\ModuleInstallerException $e) {
            $text = $e->getMessage();
        }

        return $this->redirectMessages->setMessage($result, $text, $this->request->getFullPath());
    }

    /**
     * @throws \Doctrine\DBAL\Exception
     */
    private function outputPage(): array
    {
        $this->renewCaches();

        return ($this->adminModulesViewProvider)();
    }
}
