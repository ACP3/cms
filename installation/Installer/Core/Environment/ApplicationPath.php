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
    protected $installerAppDir;
    /**
     * @var string
     */
    protected $installerCacheDir;
    /**
     * @var string
     */
    protected $installerClassesDir;
    /**
     * @var string
     */
    protected $installerModulesDir;
    /**
     * @var string
     */
    protected $installerWebRoot;

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
        $this->installerAppDir = \realpath($this->appDir . '../installation') . '/Installer/';
        $this->installerModulesDir = $this->installerAppDir . 'Modules/';
        $this->installerClassesDir = $this->installerAppDir . 'Core/';
        $this->designRootPathInternal = ACP3_ROOT_DIR . 'installation/design/';
    }

    /**
     * @return string
     */
    public function getInstallerAppDir(): string
    {
        return $this->installerAppDir;
    }

    /**
     * @return string
     */
    public function getInstallerCacheDir(): string
    {
        return $this->installerCacheDir;
    }

    /**
     * @return string
     */
    public function getInstallerClassesDir(): string
    {
        return $this->installerClassesDir;
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
