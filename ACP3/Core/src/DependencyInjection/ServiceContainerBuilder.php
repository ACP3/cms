<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Core\DependencyInjection;

use ACP3\Core\Assets\DependencyInjection\RegisterAssetLibraryPass;
use ACP3\Core\Authentication\DependencyInjection\RegisterAuthenticationsCompilerPass;
use ACP3\Core\Component\ComponentRegistry;
use ACP3\Core\Component\ComponentTypeEnum;
use ACP3\Core\Component\Dto\ComponentDataDto;
use ACP3\Core\Controller\DependencyInjection\RegisterControllerActionsPass;
use ACP3\Core\DataGrid\DependencyInjection\RegisterColumnRendererPass;
use ACP3\Core\Environment\ApplicationPath;
use ACP3\Core\Helpers\ContentDecorator\DependencyInjection\RegisterContentDecoratorPass;
use ACP3\Core\Installer\DependencyInjection\RegisterInstallersCompilerPass;
use ACP3\Core\Model\DataProcessor\DependencyInjection\RegisterColumnTypesCompilerPass;
use ACP3\Core\Validation\DependencyInjection\RegisterValidationRulesPass;
use ACP3\Core\View\Renderer\Smarty\DependencyInjection\RegisterSmartyPluginsPass;
use ACP3\Core\WYSIWYG\DependencyInjection\RegisterWysiwygEditorsCompilerPass;
use Symfony\Bridge\ProxyManager\LazyProxy\Instantiator\RuntimeInstantiator;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\EventDispatcher\DependencyInjection\RegisterListenersPass;
use Symfony\Component\HttpKernel\DependencyInjection\FragmentRendererPass;
use Symfony\Component\Mime\DependencyInjection\AddMimeTypeGuesserPass;

final class ServiceContainerBuilder extends ContainerBuilder
{
    /**
     * @var ApplicationPath
     */
    private $applicationPath;

    /**
     * @throws \MJS\TopSort\CircularDependencyException
     * @throws \MJS\TopSort\ElementNotFoundException
     */
    public function __construct(ApplicationPath $applicationPath)
    {
        parent::__construct();

        $this->applicationPath = $applicationPath;

        $this->setUpContainer();
    }

    /**
     * @throws \MJS\TopSort\CircularDependencyException
     * @throws \MJS\TopSort\ElementNotFoundException
     * @throws \Exception
     */
    private function setUpContainer(): void
    {
        $this->setProxyInstantiator(new RuntimeInstantiator());

        $this->set('core.environment.application_path', $this->applicationPath);

        $this
            ->addCompilerPass(
                new RegisterListenersPass(
                    'core.event_dispatcher',
                    'core.eventListener',
                    'core.eventSubscriber'
                )
            )
            ->addCompilerPass(new RegisterAuthenticationsCompilerPass())
            ->addCompilerPass(new RegisterAssetLibraryPass())
            ->addCompilerPass(new RegisterSmartyPluginsPass())
            ->addCompilerPass(new RegisterColumnRendererPass())
            ->addCompilerPass(new RegisterValidationRulesPass())
            ->addCompilerPass(new RegisterWysiwygEditorsCompilerPass())
            ->addCompilerPass(new RegisterControllerActionsPass())
            ->addCompilerPass(new RegisterInstallersCompilerPass())
            ->addCompilerPass(new RegisterContentDecoratorPass())
            ->addCompilerPass(new RegisterColumnTypesCompilerPass())
            ->addCompilerPass(new AddMimeTypeGuesserPass())
            ->addCompilerPass(new FragmentRendererPass());

        $loader = new YamlFileLoader($this, new FileLocator(__DIR__));
        $loader->import($this->applicationPath->getAppDir() . 'config.yml');

        $components = ComponentRegistry::filterByType(
            ComponentRegistry::allTopSorted(),
            [
                ComponentTypeEnum::CORE,
                ComponentTypeEnum::MODULE,
            ]
        );

        foreach ($components as $component) {
            $path = $component->getPath() . '/Resources/config/services.yml';

            if (is_file($path)) {
                $loader->import($path);
            }

            $this->registerCompilerPass($component);
        }

        $this->setParameter('kernel.debug', $this->applicationPath->isDebug());

        $this->compile();
    }

    /**
     * @throws \MJS\TopSort\CircularDependencyException
     * @throws \MJS\TopSort\ElementNotFoundException
     */
    public static function create(ApplicationPath $applicationPath): ServiceContainerBuilder
    {
        return new ServiceContainerBuilder($applicationPath);
    }

    private function registerCompilerPass(ComponentDataDto $moduleCoreData): void
    {
        if ($moduleCoreData->getModuleRegistration()) {
            $moduleCoreData->getModuleRegistration()->build($this);
        }
    }
}
