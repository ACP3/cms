<?php
/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licencing details.
 */

namespace ACP3\Modules\ACP3\System\Controller\Admin\Extensions;

use ACP3\Core;
use ACP3\Modules\ACP3\Permissions;
use ACP3\Modules\ACP3\System;

class Modules extends Core\Controller\AbstractFrontendAction
{
    /**
     * @var \ACP3\Core\Modules\ModuleInfoCache
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
     * @var \ACP3\Modules\ACP3\Permissions\Cache\PermissionsCacheStorage
     */
    protected $permissionsCache;
    /**
     * @var Core\I18n\DictionaryInterface
     */
    private $dictionary;
    /**
     * @var Core\Installer\SchemaRegistrar
     */
    private $schemaRegistrar;
    /**
     * @var \ACP3\Core\Installer\SchemaInstaller
     */
    private $schemaInstaller;
    /**
     * @var \ACP3\Core\Installer\AclInstaller
     */
    private $aclInstaller;
    /**
     * @var Core\View\Block\BlockInterface
     */
    private $block;

    /**
     * Modules constructor.
     *
     * @param \ACP3\Core\Controller\Context\FrontendContext $context
     * @param Core\View\Block\BlockInterface $block
     * @param Core\I18n\DictionaryInterface $dictionary
     * @param \ACP3\Core\Modules\ModuleInfoCache $moduleInfoCache
     * @param \ACP3\Modules\ACP3\System\Model\Repository\ModulesRepository $systemModuleRepository
     * @param \ACP3\Modules\ACP3\System\Helper\Installer $installerHelper
     * @param \ACP3\Modules\ACP3\Permissions\Cache\PermissionsCacheStorage $permissionsCache
     * @param Core\Installer\SchemaRegistrar $schemaRegistrar
     * @param \ACP3\Core\Installer\SchemaInstaller $schemaInstaller
     * @param \ACP3\Core\Installer\AclInstaller $aclInstaller
     */
    public function __construct(
        Core\Controller\Context\FrontendContext $context,
        Core\View\Block\BlockInterface $block,
        Core\I18n\DictionaryInterface $dictionary,
        Core\Modules\ModuleInfoCache $moduleInfoCache,
        System\Model\Repository\ModulesRepository $systemModuleRepository,
        System\Helper\Installer $installerHelper,
        Permissions\Cache\PermissionsCacheStorage $permissionsCache,
        Core\Installer\SchemaRegistrar $schemaRegistrar,
        Core\Installer\SchemaInstaller $schemaInstaller,
        Core\Installer\AclInstaller $aclInstaller
    ) {
        parent::__construct($context);

        $this->moduleInfoCache = $moduleInfoCache;
        $this->systemModuleRepository = $systemModuleRepository;
        $this->installerHelper = $installerHelper;
        $this->permissionsCache = $permissionsCache;
        $this->dictionary = $dictionary;
        $this->schemaRegistrar = $schemaRegistrar;
        $this->schemaInstaller = $schemaInstaller;
        $this->aclInstaller = $aclInstaller;
        $this->block = $block;
    }

    /**
     * @param string $action
     * @param string $dir
     *
     * @return array|\Symfony\Component\HttpFoundation\RedirectResponse
     * @throws \ACP3\Core\Controller\Exception\ResultNotExistsException
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
            case 'refresh':
                return $this->refreshCaches();
            default:
                return $this->outputPage();
        }
    }

    /**
     * @param string $moduleDirectory
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    protected function enableModule($moduleDirectory)
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
     * @throws System\Exception\ModuleInstallerException
     */
    private function checkPreconditions($moduleDirectory)
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
    protected function moduleInstallerExists($serviceId)
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
    protected function checkForFailedModuleDependencies(array $dependencies, $phrase)
    {
        if (!empty($dependencies)) {
            throw new System\Exception\ModuleInstallerException(
                $this->translator->t(
                    'system',
                    $phrase,
                    ['%modules%' => implode(', ', $dependencies)]
                )
            );
        }
    }

    /**
     * @param string $moduleDirectory
     * @param int    $active
     *
     * @return bool|int
     */
    protected function saveModuleState($moduleDirectory, $active)
    {
        return $this->systemModuleRepository->update(['active' => $active], ['name' => $moduleDirectory]);
    }

    private function purgeCaches(): bool
    {
        return Core\Cache\Purge::doPurge($this->appPath->getCacheDir());
    }

    /**
     * @param string $moduleDirectory
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     * @throws \ACP3\Core\Controller\Exception\ResultNotExistsException
     */
    protected function disableModule($moduleDirectory)
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
     */
    protected function installModule($moduleDirectory)
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
     */
    protected function uninstallModule($moduleDirectory)
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

    private function refreshCaches()
    {
        $result = $this->purgeCaches();

        return $this->redirectMessages()->setMessage(
            $result,
            $this->translator->t('system', $result ? 'cache_refresh_success' : 'cache_refresh_error'),
            $this->request->getFullPath()
        );
    }

    /**
     * @return array
     */
    protected function outputPage()
    {
        return $this->block->render();
    }
}
