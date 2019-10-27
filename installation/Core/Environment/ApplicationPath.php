<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Installer\Core\Environment;

class ApplicationPath extends \ACP3\Core\Environment\ApplicationPath
{
    /**
     * @var string
     */
    private $installerModulesDir;
    /**
     * @var string
     */
    private $installerWebRoot;

    /**
     * ApplicationPath constructor.
     *
     * @param string $applicationMode
     */
    public function __construct(string $applicationMode)
    {
        parent::__construct($applicationMode);

        $this->installerWebRoot = $this->webRoot;
        $this->webRoot = \substr($this->webRoot !== '/' ? $this->webRoot . '/' : '/', 0, -14);
        $this->installerModulesDir = \realpath($this->appDir . '../installation') . '/Modules/';
        $this->designRootPathInternal = ACP3_ROOT_DIR . 'installation/design/';
    }

    /**
     * @return string
     */
    public function getInstallerModulesDir(): string
    {
        return $this->installerModulesDir;
    }

    /**
     * @return string
     */
    public function getInstallerWebRoot(): string
    {
        return $this->installerWebRoot;
    }
}
