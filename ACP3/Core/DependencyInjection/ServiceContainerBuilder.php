<?php
/**
 * Copyright (c) 2016 by the ACP3 Developers.
 * See the LICENCE file at the top-level module directory for licencing details.
 */

namespace ACP3\Core\DependencyInjection;

use ACP3\Core\Environment\ApplicationPath;
use ACP3\Core\Helpers\DataGrid\DependencyInjection\RegisterColumnRendererPass;
use ACP3\Core\Modules;
use ACP3\Core\Validation\DependencyInjection\RegisterValidationRulesPass;
use ACP3\Core\View\Renderer\Smarty\DependencyInjection\RegisterSmartyPluginsPass;
use ACP3\Core\WYSIWYG\DependencyInjection\RegisterWysiwygEditorsCompilerPass;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\EventDispatcher\DependencyInjection\RegisterListenersPass;
use Symfony\Component\HttpFoundation\Request as SymfonyRequest;

/**
 * Class ServiceContainerBuilder
 * @package ACP3\Core\DependencyInjection
 */
class ServiceContainerBuilder extends ContainerBuilder
{
    /**
     * @var ApplicationPath
     */
    private $applicationPath;
    /**
     * @var SymfonyRequest
     */
    private $symfonyRequest;
    /**
     * @var string
     */
    private $applicationMode;
    /**
     * @var bool
     */
    private $allModules;

    /**
     * ServiceContainerBuilder constructor.
     * @param ApplicationPath $applicationPath
     * @param SymfonyRequest $symfonyRequest
     * @param string $applicationMode
     * @param bool $allModules
     */
    public function __construct(
        ApplicationPath $applicationPath,
        SymfonyRequest $symfonyRequest,
        $applicationMode,
        $allModules = false
    ) {
        parent::__construct();

        $this->applicationPath = $applicationPath;
        $this->symfonyRequest = $symfonyRequest;
        $this->applicationMode = $applicationMode;
        $this->allModules = $allModules;

        $this->setUpContainer();
    }

    private function setUpContainer()
    {
        $this->set('core.http.symfony_request', $this->symfonyRequest);
        $this->set('core.environment.application_path', $this->applicationPath);
        $this->setParameter('core.environment', $this->applicationMode);

        $this->addCompilerPass(
            new RegisterListenersPass('core.event_dispatcher', 'core.eventListener', 'core.eventSubscriber')
        );
        $this->addCompilerPass(new RegisterSmartyPluginsPass());
        $this->addCompilerPass(new RegisterColumnRendererPass());
        $this->addCompilerPass(new RegisterValidationRulesPass());
        $this->addCompilerPass(new RegisterWysiwygEditorsCompilerPass());

        $loader = new YamlFileLoader($this, new FileLocator(__DIR__));
        $loader->load($this->applicationPath->getClassesDir() . 'config/services.yml');
        $loader->load($this->applicationPath->getClassesDir() . 'View/Renderer/Smarty/config/services.yml');

        // Try to get all available services
        /** @var Modules $modules */
        $modules = $this->get('core.modules');
        $availableModules = ($this->allModules === true) ? $modules->getAllModules() : $modules->getInstalledModules();
        $vendors = $this->get('core.modules.vendors')->getVendors();

        foreach ($availableModules as $module) {
            foreach ($vendors as $vendor) {
                $path = $this->applicationPath->getModulesDir() . $vendor . '/' . $module['dir'] . '/Resources/config/services.yml';

                if (is_file($path)) {
                    $loader->load($path);
                }
            }
        }

        $this->compile();
    }

    /**
     * @param \ACP3\Core\Environment\ApplicationPath $applicationPath
     * @param SymfonyRequest $symfonyRequest
     * @param string $applicationMode
     * @param bool $allModules
     * @return ContainerBuilder
     */
    public static function create(
        ApplicationPath $applicationPath,
        SymfonyRequest $symfonyRequest,
        $applicationMode,
        $allModules = false
    ) {
        return new static($applicationPath, $symfonyRequest, $applicationMode, $allModules);
    }
}
