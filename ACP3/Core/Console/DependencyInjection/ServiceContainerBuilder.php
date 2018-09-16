<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Core\Console\DependencyInjection;

use ACP3\Core\Environment\ApplicationPath;
use ACP3\Core\Installer\DependencyInjection\RegisterInstallersCompilerPass;
use ACP3\Core\Modules\Modules;
use Psr\Log\LoggerInterface;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\EventDispatcher\DependencyInjection\RegisterListenersPass;

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
     * @var string
     */
    private $applicationMode;

    /**
     * ServiceContainerBuilder constructor.
     *
     * @param LoggerInterface $logger
     * @param ApplicationPath $applicationPath
     * @param string          $applicationMode
     */
    public function __construct(
        LoggerInterface $logger,
        ApplicationPath $applicationPath,
        string $applicationMode
    ) {
        parent::__construct();

        $this->logger = $logger;
        $this->applicationPath = $applicationPath;
        $this->applicationMode = $applicationMode;

        $this->setUpContainer();
    }

    private function setUpContainer()
    {
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
            ->addCompilerPass(new RegisterCommandsCompilerPass());

        $loader = new YamlFileLoader($this, new FileLocator(__DIR__));
        $loader->load($this->applicationPath->getClassesDir() . 'config/console.yml');

        /** @var Modules $modules */
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
     * @param LoggerInterface                        $logger
     * @param \ACP3\Core\Environment\ApplicationPath $applicationPath
     * @param string                                 $applicationMode
     *
     * @return ContainerBuilder
     */
    public static function create(
        LoggerInterface $logger,
        ApplicationPath $applicationPath,
        $applicationMode
    ) {
        return new static($logger, $applicationPath, $applicationMode);
    }
}
