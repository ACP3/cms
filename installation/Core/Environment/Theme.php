<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Installer\Core\Environment;

use ACP3\Core\Environment\ThemePathInterface;

class Theme implements ThemePathInterface
{
    /**
     * @var \ACP3\Installer\Core\Environment\ApplicationPath
     */
    private $appPath;

    public function __construct(ApplicationPath $appPath)
    {
        $this->appPath = $appPath;
    }

    public function getCurrentTheme(): string
    {
        return '';
    }

    public function getDesignPathInternal(): string
    {
        return $this->appPath->getDesignRootPathInternal();
    }

    public function getDesignPathWeb(): string
    {
        return $this->appPath->getInstallerWebRoot() . 'design/';
    }

    public function getThemeDependencies(string $themeName): array
    {
        return [];
    }

    public function getCurrentThemeDependencies(): array
    {
        return [];
    }
}
