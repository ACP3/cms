<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Installer\Core\DependencyInjection;

use ACP3\Core\Controller\DependencyInjection\RegisterControllerActionsPass;
use ACP3\Core\Environment\ApplicationMode;
use ACP3\Core\Http\RequestInterface;
use ACP3\Core\Installer\DependencyInjection\RegisterInstallersCompilerPass;
use ACP3\Core\Router\RouterInterface;
use ACP3\Core\Validation\DependencyInjection\RegisterValidationRulesPass;
use ACP3\Core\View\Renderer\Smarty\DependencyInjection\RegisterLegacySmartyPluginsPass;
use ACP3\Core\View\Renderer\Smarty\DependencyInjection\RegisterSmartyPluginsPass;
use ACP3\Installer\Core\Environment\ApplicationPath;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\EventDispatcher\DependencyInjection\RegisterListenersPass;
use Symfony\Component\HttpFoundation\Request;

class ServiceContainerBuilder extends ContainerBuilder
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
    private $includeModules;

    /**
     * ServiceContainerBuilder constructor.
     *
     * @param ApplicationPath $applicationPath
     * @param Request         $symfonyRequest
     * @param string          $applicationMode
     * @param bool            $includeModules
     *
     * @throws \Exception
     */
    public function __construct(
        ApplicationPath $applicationPath,
        Request $symfonyRequest,
        string $applicationMode,
        bool $includeModules = false
    ) {
        parent::__construct();

        $this->applicationPath = $applicationPath;
        $this->symfonyRequest = $symfonyRequest;
        $this->applicationMode = $applicationMode;
        $this->includeModules = $includeModules;

        $this->setUpContainer();
    }

    /**
     * @throws \Exception
     */
    private function setUpContainer()
    {
        $this->setParameter('cache_driver', 'Array');
        $this->set('core.http.symfony_request', $this->symfonyRequest);
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
            ->addCompilerPass(new RegisterLegacySmartyPluginsPass())
            ->addCompilerPass(new RegisterControllerActionsPass())
            ->addCompilerPass(new RegisterValidationRulesPass())
            ->addCompilerPass(new RegisterInstallersCompilerPass());

        $loader = new YamlFileLoader($this, new FileLocator(__DIR__));

        $request = $router = null;

        if ($this->applicationMode === ApplicationMode::UPDATER) {
            $loader->load($this->applicationPath->getInstallerClassesDir() . 'config/services_extended.yml');

            /** @var RequestInterface $request */
            $request = $this->get('core.http.request');
            /** @var RouterInterface $router */
            $router = $this->get('core.router');

            if ($this->canIncludeModules() === true) {
                $loader->load($this->applicationPath->getClassesDir() . 'config/services.yml');
            }
        } else {
            if ($this->canIncludeModules() === true) {
                $loader->load($this->applicationPath->getClassesDir() . 'config/services.yml');
            }

            $loader->load($this->applicationPath->getInstallerClassesDir() . 'config/services.yml');
        }

        $this->includeModules($loader, $request, $router);

        $this->compile();
    }

    /**
     * @return bool
     */
    private function canIncludeModules()
    {
        return $this->includeModules === true;
    }

    /**
     * @param YamlFileLoader                         $loader
     * @param \ACP3\Core\Http\RequestInterface|null  $request
     * @param \ACP3\Core\Router\RouterInterface|null $router
     *
     * @throws \MJS\TopSort\CircularDependencyException
     * @throws \MJS\TopSort\ElementNotFoundException
     * @throws \Exception
     */
    private function includeModules(
        YamlFileLoader $loader,
        ?RequestInterface $request,
        ?RouterInterface $router
    ) {
        if (!$this->canIncludeModules()) {
            return;
        }

        $request = $request ?: $this->get('core.http.request');
        $router = $router ?: $this->get('core.router');
        /** @var \ACP3\Core\Modules $modules */
        $modules = $this->get('core.modules');

        foreach ($modules->getAllModulesTopSorted() as $module) {
            $modulePath = $this->applicationPath->getModulesDir() . $module['vendor'] . '/' . $module['dir'];
            $path = $modulePath . '/Resources/config/services.yml';

            if (\is_file($path)) {
                $loader->load($path);
            }
        }

        $this->set('core.http.request', $request);
        $this->set('core.router', $router);
    }

    /**
     * @param ApplicationPath $applicationPath
     * @param Request         $symfonyRequest
     * @param string          $applicationMode
     * @param bool            $includeModules
     *
     * @return \ACP3\Installer\Core\DependencyInjection\ServiceContainerBuilder
     *
     * @throws \Exception
     */
    public static function create(
        ApplicationPath $applicationPath,
        Request $symfonyRequest,
        string $applicationMode,
        bool $includeModules = false
    ) {
        return new static($applicationPath, $symfonyRequest, $applicationMode, $includeModules);
    }
}
