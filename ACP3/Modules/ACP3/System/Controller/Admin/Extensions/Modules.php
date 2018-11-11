<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\System\Controller\Admin\Extensions;

use ACP3\Core;
use ACP3\Modules\ACP3\Permissions;
use ACP3\Modules\ACP3\System;

class Modules extends Core\Controller\AbstractFormAction
{
    /**
     * @var \ACP3\Core\Modules\ModuleInfoCacheInterface
     */
    protected $moduleInfoCache;
    /**
     * @var \ACP3\Modules\ACP3\System\Model\Repository\ModulesRepository
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
     * @var Core\I18n\DictionaryCache
     */
    private $dictionaryCache;
    /**
     * @var Core\Installer\SchemaRegistrar
     */
    private $schemaRegistrar;
    /**
     * @var Core\Modules\SchemaInstaller
     */
    private $schemaInstaller;
    /**
     * @var Core\Modules\AclInstaller
     */
    private $aclInstaller;

    public function __construct(
        Core\Controller\Context\FormContext $context,
        Core\I18n\DictionaryCache $dictionaryCache,
        Core\Modules\ModuleInfoCacheInterface $moduleInfoCache,
        System\Model\Repository\ModulesRepository $systemModuleRepository,
        System\Helper\Installer $installerHelper,
        Permissions\Cache $permissionsCache,
        Core\Installer\SchemaRegistrar $schemaRegistrar,
        Core\Modules\SchemaInstaller $schemaInstaller,
        Core\Modules\AclInstaller $aclInstaller
    ) {
        parent::__construct($context);

        $this->moduleInfoCache = $moduleInfoCache;
        $this->systemModuleRepository = $systemModuleRepository;
        $this->installerHelper = $installerHelper;
        $this->permissionsCache = $permissionsCache;
        $this->dictionaryCache = $dictionaryCache;
        $this->schemaRegistrar = $schemaRegistrar;
        $this->schemaInstaller = $schemaInstaller;
        $this->aclInstaller = $aclInstaller;
    }

    /**
     * @param string $action
     * @param string $dir
     *
     * @return array|\Symfony\Component\HttpFoundation\RedirectResponse
     *
     * @throws \Doctrine\DBAL\ConnectionException
     * @throws \Doctrine\DBAL\DBALException
     * @throws \MJS\TopSort\CircularDependencyException
     * @throws \MJS\TopSort\ElementNotFoundException
     */
    public function execute(string $action = '', string $dir = '')
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
     * @param string $moduleDirectory
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     *
     * @throws \Doctrine\DBAL\DBALException
     */
    protected function enableModule(string $moduleDirectory)
    {
        $result = false;

        try {
            $this->checkPreconditions($moduleDirectory);

            $this->moduleInstallerExists($moduleDirectory);

            $moduleSchema = $this->schemaRegistrar->get($moduleDirectory);

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

        return $this->redirectMessages()->setMessage($result, $text, $this->request->getFullPath());
    }

    /**
     * @param string $moduleDirectory
     *
     * @throws System\Exception\ModuleInstallerException
     */
    private function checkPreconditions(string $moduleDirectory)
    {
        $info = $this->modules->getModuleInfo($moduleDirectory);
        if (empty($info) || $info['protected'] === true || $info['installable'] === false) {
            throw new System\Exception\ModuleInstallerException(
                $this->translator->t('system', 'could_not_complete_request')
            );
        }
    }

    /**
     * @param string $serviceId
     *
     * @throws System\Exception\ModuleInstallerException
     */
    protected function moduleInstallerExists(string $serviceId)
    {
        if ($this->schemaRegistrar->has($serviceId) === false) {
            throw new System\Exception\ModuleInstallerException(
                $this->translator->t('system', 'module_installer_not_found')
            );
        }
    }

    /**
     * @param array  $dependencies
     * @param string $phrase
     *
     * @throws \ACP3\Modules\ACP3\System\Exception\ModuleInstallerException
     */
    protected function checkForFailedModuleDependencies(array $dependencies, string $phrase)
    {
        if (!empty($dependencies)) {
            throw new System\Exception\ModuleInstallerException(
                $this->translator->t(
                    'system',
                    $phrase,
                    ['%modules%' => \implode(', ', $dependencies)]
                )
            );
        }
    }

    /**
     * @param string $moduleDirectory
     * @param int    $active
     *
     * @return bool|int
     *
     * @throws \Doctrine\DBAL\DBALException
     */
    protected function saveModuleState(string $moduleDirectory, int $active)
    {
        return $this->systemModuleRepository->update(['active' => $active], ['name' => $moduleDirectory]);
    }

    protected function purgeCaches()
    {
        Core\Cache\Purge::doPurge([
            $this->appPath->getCacheDir() . 'http',
            $this->appPath->getCacheDir() . 'sql',
            $this->appPath->getCacheDir() . 'tpl_compiled',
            $this->appPath->getCacheDir() . 'tpl_cached',
            $this->appPath->getCacheDir() . 'container.php',
            $this->appPath->getCacheDir() . 'container.php.meta',
        ]);
    }

    /**
     * @throws \MJS\TopSort\CircularDependencyException
     * @throws \MJS\TopSort\ElementNotFoundException
     */
    protected function renewCaches()
    {
        $this->dictionaryCache->saveLanguageCache($this->translator->getLocale());
        $this->moduleInfoCache->saveModulesInfoCache();
        $this->permissionsCache->saveResourcesCache();
    }

    /**
     * @param string $moduleDirectory
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     *
     * @throws \Doctrine\DBAL\DBALException
     */
    protected function disableModule(string $moduleDirectory)
    {
        $result = false;

        try {
            $this->checkPreconditions($moduleDirectory);

            $this->moduleInstallerExists($moduleDirectory);

            $moduleSchema = $this->schemaRegistrar->get($moduleDirectory);

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

        return $this->redirectMessages()->setMessage($result, $text, $this->request->getFullPath());
    }

    /**
     * @param string $moduleDirectory
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     *
     * @throws \Doctrine\DBAL\DBALException
     */
    protected function installModule(string $moduleDirectory)
    {
        $result = false;

        try {
            if ($this->modules->isInstalled($moduleDirectory) === true) {
                throw new System\Exception\ModuleInstallerException(
                    $this->translator->t('system', 'module_already_installed')
                );
            }

            $this->moduleInstallerExists($moduleDirectory);

            $moduleSchema = $this->schemaRegistrar->get($moduleDirectory);

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

        return $this->redirectMessages()->setMessage($result, $text, $this->request->getFullPath());
    }

    /**
     * @param string $moduleDirectory
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     *
     * @throws \Doctrine\DBAL\ConnectionException
     */
    protected function uninstallModule(string $moduleDirectory)
    {
        $result = false;

        try {
            $info = $this->modules->getModuleInfo($moduleDirectory);
            if ($this->modules->isInstalled($moduleDirectory) === false || $info['protected'] === true) {
                throw new System\Exception\ModuleInstallerException(
                    $this->translator->t('system', 'protected_module_description')
                );
            }

            $this->moduleInstallerExists($moduleDirectory);

            $moduleSchema = $this->schemaRegistrar->get($moduleDirectory);

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

        return $this->redirectMessages()->setMessage($result, $text, $this->request->getFullPath());
    }

    /**
     * @return array
     *
     * @throws \MJS\TopSort\CircularDependencyException
     * @throws \MJS\TopSort\ElementNotFoundException
     */
    protected function outputPage()
    {
        $this->renewCaches();

        $installedModules = $newModules = [];

        foreach ($this->modules->getAllModulesAlphabeticallySorted() as $key => $values) {
            $values['dir'] = \strtolower($values['dir']);
            if ($this->modules->isInstalled($values['dir']) === true || $values['installable'] === false) {
                $installedModules[$key] = $values;
            } else {
                $newModules[$key] = $values;
            }
        }

        return [
            'installed_modules' => $installedModules,
            'new_modules' => $newModules,
        ];
    }
}
