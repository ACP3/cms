<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Core\Console\DependencyInjection;

use ACP3\Core\Component\ComponentRegistry;
use ACP3\Core\Environment\ApplicationPath;
use ACP3\Core\Installer\DependencyInjection\RegisterInstallersCompilerPass;
use ACP3\Core\Model\DataProcessor\DependencyInjection\RegisterColumnTypesCompilerPass;
use Psr\Log\LoggerInterface;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\EventDispatcher\DependencyInjection\RegisterListenersPass;
use Symfony\Component\HttpFoundation\Request;

final class ServiceContainerBuilder extends ContainerBuilder
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
        $loader->import($this->applicationPath->getAppDir() . 'config.yml');
        $loader->import(ComponentRegistry::getPathByName('core') . '/Resources/config/console.yml');

        foreach (ComponentRegistry::allTopSorted() as $module) {
            $path = $module->getPath() . '/Resources/config/services.yml';

            if (\is_file($path)) {
                $loader->import($path);
            }
        }

        $this->compile();
    }

    /**
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
