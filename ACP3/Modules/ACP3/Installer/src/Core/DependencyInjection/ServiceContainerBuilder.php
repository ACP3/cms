<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Installer\Core\DependencyInjection;

use ACP3\Core\Component\ComponentRegistry;
use ACP3\Core\Controller\DependencyInjection\RegisterControllerActionsPass;
use ACP3\Core\Environment\ApplicationMode;
use ACP3\Core\Installer\DependencyInjection\RegisterInstallersCompilerPass;
use ACP3\Core\Validation\DependencyInjection\RegisterValidationRulesPass;
use ACP3\Core\View\Renderer\Smarty\DependencyInjection\RegisterLegacySmartyPluginsPass;
use ACP3\Core\View\Renderer\Smarty\DependencyInjection\RegisterSmartyPluginsPass;
use ACP3\Modules\ACP3\Installer\Core\Environment\ApplicationPath;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\EventDispatcher\DependencyInjection\RegisterListenersPass;
use Symfony\Component\HttpFoundation\Request;

final class ServiceContainerBuilder extends ContainerBuilder
{
    /**
     * @var ApplicationPath
     */
    private $applicationPath;
    /**
     * @var Request
     */
    private $symfonyRequest;
    /**
     * @var string
     */
    private $applicationMode;
    /**
     * @var bool
     */
    private $isInstallingOrUpdating;

    /**
     * ServiceContainerBuilder constructor.
     *
     * @throws \Exception
     */
    public function __construct(
        ApplicationPath $applicationPath,
        Request $symfonyRequest,
        string $applicationMode,
        bool $isInstallingOrUpdating = false
    ) {
        parent::__construct();

        $this->applicationPath = $applicationPath;
        $this->symfonyRequest = $symfonyRequest;
        $this->applicationMode = $applicationMode;
        $this->isInstallingOrUpdating = $isInstallingOrUpdating;

        $this->setUpContainer();
    }

    /**
     * @throws \Exception
     */
    private function setUpContainer(): void
    {
        $this->set('core.environment.application_path', $this->applicationPath);
        $this->set('core.http.symfony_request', $this->symfonyRequest);

        $this
            ->addCompilerPass(
                new RegisterListenersPass(
                    'core.event_dispatcher',
                    'core.eventListener',
                    'core.eventSubscriber'
                )
            )
            ->addCompilerPass(new RegisterSmartyPluginsPass())
            ->addCompilerPass(new RegisterLegacySmartyPluginsPass())
            ->addCompilerPass(new RegisterControllerActionsPass())
            ->addCompilerPass(new RegisterValidationRulesPass())
            ->addCompilerPass(new RegisterInstallersCompilerPass());

        $loader = new YamlFileLoader($this, new FileLocator(__DIR__));

        $this->includeCoreServices($loader);

        foreach (ComponentRegistry::allTopSorted() as $module) {
            $loader->import($module->getPath() . '/Resources/config/services.yml');
        }

        if ($this->isInstallingOrUpdating === false) {
            $loader->import(ComponentRegistry::getPathByName('installer') . '/Resources/config/services_overrides.yml');
        }

        if ($this->applicationMode === ApplicationMode::UPDATER) {
            $loader->import(ComponentRegistry::getPathByName('installer') . '/Resources/config/services_updater.yml');
        }

        $this->compile();
    }

    /**
     * @throws \Exception
     */
    private function includeCoreServices(YamlFileLoader $loader): void
    {
        if ($this->isInstallingOrUpdating === false) {
            $this->setParameter('db_host', '');
            $this->setParameter('db_name', '');
            $this->setParameter('db_table_prefix', '');
            $this->setParameter('db_password', '');
            $this->setParameter('db_user', '');
            $this->setParameter('db_driver', 'pdo_mysql');
            $this->setParameter('db_charset', 'utf8mb4');

            return;
        }

        $loader->import($this->applicationPath->getAppDir() . 'config.yml');
    }

    /**
     * @return \ACP3\Modules\ACP3\Installer\Core\DependencyInjection\ServiceContainerBuilder
     *
     * @throws \Exception
     */
    public static function create(
        ApplicationPath $applicationPath,
        Request $symfonyRequest,
        string $applicationMode,
        bool $isInstallingOrUpdating = false
    ): ServiceContainerBuilder {
        return new static($applicationPath, $symfonyRequest, $applicationMode, $isInstallingOrUpdating);
    }
}
