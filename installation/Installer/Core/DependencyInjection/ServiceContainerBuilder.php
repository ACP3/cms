<?php
/**
 * Copyright (c) 2016 by the ACP3 Developers.
 * See the LICENCE file at the top-level module directory for licencing details.
 */

namespace ACP3\Installer\Core\DependencyInjection;

use ACP3\Core\Environment\ApplicationMode;
use ACP3\Core\Installer\DependencyInjection\RegisterInstallersCompilerPass;
use ACP3\Core\Validation\DependencyInjection\RegisterValidationRulesPass;
use ACP3\Core\View\Renderer\Smarty\DependencyInjection\RegisterSmartyPluginsPass;
use ACP3\Installer\Core\Environment\ApplicationPath;
use Psr\Log\LoggerInterface;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\EventDispatcher\DependencyInjection\RegisterListenersPass;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class ServiceContainerBuilder
 * @package ACP3\Installer\Core\DependencyInjection
 */
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
     * @var LoggerInterface
     */
    private $logger;

    /**
     * ServiceContainerBuilder constructor.
     * @param LoggerInterface $logger
     * @param ApplicationPath $applicationPath
     * @param Request $symfonyRequest
     * @param string $applicationMode
     * @param bool $includeModules
     */
    public function __construct(
        LoggerInterface $logger,
        ApplicationPath $applicationPath,
        Request $symfonyRequest,
        $applicationMode,
        $includeModules = false
    ) {
        parent::__construct();

        $this->logger = $logger;
        $this->applicationPath = $applicationPath;
        $this->symfonyRequest = $symfonyRequest;
        $this->applicationMode = $applicationMode;
        $this->includeModules = $includeModules;

        $this->setUpContainer();
    }

    private function setUpContainer()
    {
        $this->setParameter('cache_driver', 'Array');
        $this->setParameter('core.environment', $this->applicationMode);
        $this->set('core.http.symfony_request', $this->symfonyRequest);
        $this->set('core.environment.application_path', $this->applicationPath);
        $this->set('core.logger.system_logger', $this->logger);
        $this->addCompilerPass(
            new RegisterListenersPass('core.event_dispatcher', 'core.eventListener', 'core.eventSubscriber')
        );
        $this->addCompilerPass(new RegisterSmartyPluginsPass());
        $this->addCompilerPass(new RegisterValidationRulesPass());

        $loader = new YamlFileLoader($this, new FileLocator(__DIR__));

        if ($this->canIncludeModules() === true) {
            $this->addCompilerPass(new RegisterInstallersCompilerPass());

            $loader->load($this->applicationPath->getClassesDir() . 'config/services.yml');
        }

        $loader->load($this->applicationPath->getInstallerClassesDir() . 'config/services.yml');
        if ($this->applicationMode === ApplicationMode::UPDATER) {
            $loader->load($this->applicationPath->getInstallerClassesDir() . 'config/update.yml');
        }

        $this->includeModules($loader);

        $this->compile();
    }

    /**
     * @return bool
     */
    private function canIncludeModules()
    {
        return $this->applicationMode === ApplicationMode::UPDATER || $this->includeModules === true;
    }

    /**
     * @param YamlFileLoader $loader
     */
    private function includeModules(YamlFileLoader $loader)
    {
        if ($this->canIncludeModules() === true) {
            $request = $this->get('core.http.request');
            $router = $this->get('core.router');

            $vendors = $this->get('core.modules.vendors')->getVendors();
            foreach ($vendors as $vendor) {
                $namespaceModules = glob($this->applicationPath->getModulesDir() . $vendor . '/*/Resources/config/services.yml');
                foreach ($namespaceModules as $module) {
                    $loader->load($module);
                }
            }

            $this->set('core.http.request', $request);
            $this->set('core.router', $router);
        }
    }

    /**
     * @param LoggerInterface $logger
     * @param ApplicationPath $applicationPath
     * @param Request $symfonyRequest
     * @param string $applicationMode
     * @param bool $includeModules
     * @return ContainerBuilder
     */
    public static function create(
        LoggerInterface $logger,
        ApplicationPath $applicationPath,
        Request $symfonyRequest,
        $applicationMode,
        $includeModules = false)
    {
        return new static($logger, $applicationPath, $symfonyRequest, $applicationMode, $includeModules);
    }
}
