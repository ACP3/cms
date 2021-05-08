<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Installer\Core\DependencyInjection;

use ACP3\Core\Assets\DependencyInjection\RegisterAssetLibraryPass;
use ACP3\Core\Component\ComponentRegistry;
use ACP3\Core\Component\ComponentTypeEnum;
use ACP3\Core\Controller\DependencyInjection\RegisterControllerActionsPass;
use ACP3\Core\Environment\ApplicationMode;
use ACP3\Core\Installer\DependencyInjection\RegisterInstallersCompilerPass;
use ACP3\Core\Validation\DependencyInjection\RegisterValidationRulesPass;
use ACP3\Core\View\Renderer\Smarty\DependencyInjection\RegisterSmartyPluginsPass;
use ACP3\Modules\ACP3\Installer\Core\Environment\ApplicationPath;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\EventDispatcher\DependencyInjection\RegisterListenersPass;
use Symfony\Component\HttpKernel\DependencyInjection\FragmentRendererPass;

final class ServiceContainerBuilder extends ContainerBuilder
{
    /**
     * @var ApplicationPath
     */
    private $applicationPath;
    /**
     * @var bool
     */
    private $isInstallingOrUpdating;

    /**
     * @throws \Exception
     */
    public function __construct(
        ApplicationPath $applicationPath,
        bool $isInstallingOrUpdating = false
    ) {
        parent::__construct();

        $this->applicationPath = $applicationPath;
        $this->isInstallingOrUpdating = $isInstallingOrUpdating;

        $this->setUpContainer();
    }

    /**
     * @throws \Exception
     */
    private function setUpContainer(): void
    {
        $this->set('core.environment.application_path', $this->applicationPath);

        $this
            ->addCompilerPass(
                new RegisterListenersPass(
                    'core.event_dispatcher',
                    'core.eventListener',
                    'core.eventSubscriber'
                )
            )
            ->addCompilerPass(new RegisterSmartyPluginsPass())
            ->addCompilerPass(new RegisterControllerActionsPass())
            ->addCompilerPass(new RegisterValidationRulesPass())
            ->addCompilerPass(new RegisterInstallersCompilerPass())
            ->addCompilerPass(new RegisterAssetLibraryPass())
            ->addCompilerPass(new FragmentRendererPass());

        $loader = new YamlFileLoader($this, new FileLocator(__DIR__));

        $this->includeCoreServices($loader);

        // Themes currently don't define or override services, so we have to filter them out.
        // Otherwise we would get errors...
        $filteredComponents = ComponentRegistry::excludeByType(
            ComponentRegistry::allTopSorted(),
            [ComponentTypeEnum::THEME]
        );

        foreach ($filteredComponents as $component) {
            $loader->import($component->getPath() . '/Resources/config/services.yml');
        }

        if ($this->isInstallingOrUpdating === false) {
            $loader->import(ComponentRegistry::getPathByName('installer') . '/Resources/config/services_overrides.yml');
        }

        if ($this->applicationPath->getApplicationMode() === ApplicationMode::UPDATER) {
            $loader->import(ComponentRegistry::getPathByName('installer') . '/Resources/config/services_updater.yml');
        }

        $this->removeDefinition('http_cache');

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
     * @throws \Exception
     */
    public static function create(
        ApplicationPath $applicationPath,
        bool $isInstallingOrUpdating = false
    ): ServiceContainerBuilder {
        return new ServiceContainerBuilder($applicationPath, $isInstallingOrUpdating);
    }
}
