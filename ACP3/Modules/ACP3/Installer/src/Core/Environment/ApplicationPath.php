<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Installer\Core\Environment;

use ACP3\Core\Environment\ApplicationMode;

class ApplicationPath extends \ACP3\Core\Environment\ApplicationPath
{
    private readonly string $installerWebRoot;

    public function __construct(ApplicationMode $applicationMode)
    {
        parent::__construct($applicationMode);

        $this->installerWebRoot = $this->getWebRoot();
        $this->setWebRoot(
            substr($this->getWebRoot() !== '/' ? $this->getWebRoot() . '/' : '/', 0, -14)
        );
    }

    public function getInstallerWebRoot(): string
    {
        return $this->installerWebRoot;
    }
}
