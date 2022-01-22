<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Installer\Core\Application;

use ACP3\Core;
use ACP3\Core\Environment\ApplicationMode;
use ACP3\Modules\ACP3\Installer\Core\DependencyInjection\ServiceContainerBuilder;
use ACP3\Modules\ACP3\Installer\Core\Environment\ApplicationPath;

class Bootstrap extends Core\Application\AbstractBootstrap
{
    /**
     * @var \ACP3\Modules\ACP3\Installer\Core\Environment\ApplicationPath ApplicationPath
     */
    protected $appPath;

    /**
     * {@inheritdoc}
     */
    public function isInstalled(): bool
    {
        // Standardzeitzone festlegen
        date_default_timezone_set('UTC');

        if ($this->appMode === ApplicationMode::UPDATER) {
            return $this->databaseConfigExists();
        }

        return true;
    }

    protected function initializeApplicationPath(): void
    {
        $this->appPath = new ApplicationPath($this->appMode);
    }

    /**
     * {@inheritdoc}
     *
     * @throws \Exception
     */
    public function initializeClasses(): void
    {
        $this->container = ServiceContainerBuilder::create($this->appPath);
        $this->container->set('kernel', $this);
    }
}
