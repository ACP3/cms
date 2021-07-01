<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\System\Controller\Admin\Extensions;

use ACP3\Core;
use ACP3\Core\Controller\Context\WidgetContext;
use ACP3\Core\Helpers\RedirectMessages;
use ACP3\Modules\ACP3\System\Event\RenewCacheEvent;
use ACP3\Modules\ACP3\System\Exception\ModuleInstallerException;
use ACP3\Modules\ACP3\System\Helper\Installer;
use ACP3\Modules\ACP3\System\Services\CacheClearService;
use ACP3\Modules\ACP3\System\ViewProviders\AdminModulesViewProvider;
use Doctrine\DBAL\Exception as DBALException;
use Psr\Container\ContainerInterface;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;

class Modules extends Core\Controller\AbstractWidgetAction
{
    /**
     * @var Installer
     */
    private $installerHelper;
    /**
     * @var ContainerInterface
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
     * @var Core\Modules
     */
    private $modules;
    /**
     * @var AdminModulesViewProvider
     */
    private $adminModulesViewProvider;
    /**
     * @var RedirectMessages
     */
    private $redirectMessages;
    /**
     * @var CacheClearService
     */
    private $cacheClearService;
    /**
     * @var EventDispatcher
     */
    private $eventDispatcher;

    public function __construct(
        WidgetContext $context,
        EventDispatcher $eventDispatcher,
        RedirectMessages $redirectMessages,
        Core\Modules $modules,
        Installer $installerHelper,
        ContainerInterface $schemaLocator,
        Core\Modules\SchemaInstaller $schemaInstaller,
        Core\Modules\AclInstaller $aclInstaller,
        AdminModulesViewProvider $adminModulesViewProvider,
        CacheClearService $cacheClearService
    ) {
        parent::__construct($context);

        $this->installerHelper = $installerHelper;
        $this->schemaLocator = $schemaLocator;
        $this->schemaInstaller = $schemaInstaller;
        $this->aclInstaller = $aclInstaller;
        $this->modules = $modules;
        $this->adminModulesViewProvider = $adminModulesViewProvider;
        $this->redirectMessages = $redirectMessages;
        $this->cacheClearService = $cacheClearService;
        $this->eventDispatcher = $eventDispatcher;
    }

    /**
     * @return array|RedirectResponse
     *
     * @throws DBALException
     */
    public function execute(?string $action = null, ?string $dir = null)
    {
        switch ($action) {
            case 'install':
                return $this->installModule($dir);
            case 'uninstall':
                return $this->uninstallModule($dir);
            default:
                return $this->outputPage();
        }
    }

    /**
     * @throws ModuleInstallerException
     */
    private function moduleInstallerExists(string $serviceId): void
    {
        if ($this->schemaLocator->has($serviceId) === false) {
            throw new ModuleInstallerException($this->translator->t('system', 'module_installer_not_found'));
        }
    }

    /**
     * @throws ModuleInstallerException
     */
    private function checkForFailedModuleDependencies(array $dependencies, string $phrase): void
    {
        if (!empty($dependencies)) {
            throw new ModuleInstallerException($this->translator->t('system', $phrase, ['%modules%' => implode(', ', $dependencies)]));
        }
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
     * @throws DBALException
     */
    private function renewCaches(): void
    {
        $this->eventDispatcher->dispatch(new RenewCacheEvent());
    }

    /**
     * @return JsonResponse|RedirectResponse
     *
     * @throws DBALException
     */
    private function installModule(string $moduleDirectory)
    {
        $result = false;

        try {
            if ($this->modules->isInstalled($moduleDirectory) === true) {
                throw new ModuleInstallerException($this->translator->t('system', 'module_already_installed'));
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
        } catch (ModuleInstallerException $e) {
            $text = $e->getMessage();
        }

        return $this->redirectMessages->setMessage($result, $text, $this->request->getFullPath());
    }

    /**
     * @return JsonResponse|RedirectResponse
     */
    private function uninstallModule(string $moduleDirectory)
    {
        $result = false;

        try {
            if ($this->modules->isInstalled($moduleDirectory) === false) {
                throw new ModuleInstallerException(sprintf($this->translator->t('system', 'module_not_installed'), $moduleDirectory));
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
        } catch (ModuleInstallerException $e) {
            $text = $e->getMessage();
        }

        return $this->redirectMessages->setMessage($result, $text, $this->request->getFullPath());
    }

    /**
     * @throws DBALException
     */
    private function outputPage(): array
    {
        $this->renewCaches();

        return ($this->adminModulesViewProvider)();
    }
}
