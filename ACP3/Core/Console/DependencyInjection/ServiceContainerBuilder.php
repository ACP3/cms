<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Core\Console\DependencyInjection;

use ACP3\Core\Environment\ApplicationPath;
use ACP3\Core\Installer\DependencyInjection\RegisterInstallersCompilerPass;
use ACP3\Core\Model\DataProcessor\DependencyInjection\RegisterColumnTypesCompilerPass;
use Psr\Log\LoggerInterface;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\EventDispatcher\DependencyInjection\RegisterListenersPass;
use Symfony\Component\HttpFoundation\Request;

class ServiceContainerBuilder extends ContainerBuilder
{
    /**
     * @var LoggerInterface
     */
    private $logger;
    /**
     * @var ApplicationPath
     */
    private $applicationPath;

    /**
     * ServiceContainerBuilder constructor.
     *
     * @param LoggerInterface $logger
     * @param ApplicationPath $applicationPath
     *
     * @throws \Exception
     */
    public function __construct(
        LoggerInterface $logger,
        ApplicationPath $applicationPath
    ) {
        parent::__construct();

        $this->logger = $logger;
        $this->applicationPath = $applicationPath;

        $this->setUpContainer();
    }

    /**
     * @throws \Exception
     */
    private function setUpContainer()
    {
        $this->set('core.http.symfony_request', Request::create(''));
        $this->set('core.logger.system_logger', $this->logger);
        $this->set('core.environment.application_path', $this->applicationPath);

        $this
            ->addCompilerPass(
                new RegisterListenersPass(
                    'core.event_dispatcher',
                    'core.eventListener',
                    'core.eventSubscriber'
                )
            )
            ->addCompilerPass(new RegisterInstallersCompilerPass())
            ->addCompilerPass(new RegisterCommandsCompilerPass())
            ->addCompilerPass(new RegisterColumnTypesCompilerPass());

        $loader = new YamlFileLoader($this, new FileLocator(__DIR__));
        $loader->load($this->applicationPath->getAppDir() . 'config.yml');
        $loader->load($this->applicationPath->getModulesDir() . 'ACP3/System/Resources/config/services.yml');
        $loader->load(\dirname(__DIR__, 2) . '/config/console.yml');

        /** @var \ACP3\Core\Modules $modules */
        $modules = $this->get('core.modules');

        foreach ($modules->getAllModulesTopSorted() as $module) {
            $modulePath = $this->applicationPath->getModulesDir() . $module['vendor'] . '/' . $module['dir'];
            $path = $modulePath . '/Resources/config/services.yml';

            if (\is_file($path)) {
                $loader->load($path);
            }
        }

        $this->compile();
    }

    /**
     * @param \Psr\Log\LoggerInterface               $logger
     * @param \ACP3\Core\Environment\ApplicationPath $applicationPath
     *
     * @return \ACP3\Core\Console\DependencyInjection\ServiceContainerBuilder
     *
     * @throws \Exception
     */
    public static function create(
        LoggerInterface $logger, ApplicationPath $applicationPath
    ) {
        return new static($logger, $applicationPath);
    }
}
