<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Core\Environment;

class ApplicationPath
{
    /**
     * @var string
     */
    private $phpSelf;
    /**
     * @var string
     */
    private $webRoot;
    /**
     * @var string
     */
    private $appDir;
    /**
     * @var string
     */
    private $classesDir;
    /**
     * @var string
     */
    private $modulesDir;
    /**
     * @var string
     */
    private $uploadsDir;
    /**
     * @var string
     */
    private $cacheDir;
    /**
     * @var string
     */
    private $designRootPathInternal;
    /**
     * @var string
     */
    private $applicationMode;
    /**
     * @var bool
     */
    private $debug;

    public function __construct(string $applicationMode)
    {
        $this->phpSelf = \htmlentities($_SERVER['SCRIPT_NAME']);
        $this->webRoot = \substr($this->phpSelf, 0, \strrpos($this->phpSelf, '/') + 1);
        $this->appDir = ACP3_ROOT_DIR . '/ACP3/';
        $this->classesDir = $this->appDir . '/Core/';
        $this->modulesDir = $this->appDir . '/Modules/';
        $this->uploadsDir = ACP3_ROOT_DIR . '/uploads/';
        $this->cacheDir = ACP3_ROOT_DIR . '/cache/' . $applicationMode . '/';
        $this->designRootPathInternal = ACP3_ROOT_DIR . '/designs/';
        $this->applicationMode = $applicationMode;
        $this->debug = $applicationMode === ApplicationMode::DEVELOPMENT;
    }

    public function getPhpSelf(): string
    {
        return $this->phpSelf;
    }

    public function getWebRoot(): string
    {
        return $this->webRoot;
    }

    protected function setWebRoot(string $webRoot): self
    {
        $this->webRoot = $webRoot;

        return $this;
    }

    public function getAppDir(): string
    {
        return $this->appDir;
    }

    public function getClassesDir(): string
    {
        return $this->classesDir;
    }

    public function getModulesDir(): string
    {
        return $this->modulesDir;
    }

    public function getUploadsDir(): string
    {
        return $this->uploadsDir;
    }

    public function getCacheDir(): string
    {
        return $this->cacheDir;
    }

    public function getDesignRootPathInternal(): string
    {
        return $this->designRootPathInternal;
    }

    /**
     * @param string $designRootPathInternal
     *
     * @return ApplicationPath
     */
    public function setDesignRootPathInternal($designRootPathInternal)
    {
        $this->designRootPathInternal = $designRootPathInternal;

        return $this;
    }

    public function getApplicationMode(): string
    {
        return $this->applicationMode;
    }

    public function isDebug(): bool
    {
        return $this->debug;
    }
}
