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
    protected $phpSelf;
    /**
     * @var string
     */
    protected $webRoot;
    /**
     * @var string
     */
    protected $appDir;
    /**
     * @var string
     */
    protected $classesDir;
    /**
     * @var string
     */
    protected $modulesDir;
    /**
     * @var string
     */
    protected $uploadsDir;
    /**
     * @var string
     */
    protected $cacheDir;
    /**
     * @var string
     */
    protected $designRootPathInternal;

    /**
     * ApplicationPath constructor.
     *
     * @param string $applicationMode
     */
    public function __construct(string $applicationMode)
    {
        $this->phpSelf = \htmlentities($_SERVER['SCRIPT_NAME']);
        $this->webRoot = \substr($this->phpSelf, 0, \strrpos($this->phpSelf, '/') + 1);
        $this->appDir = ACP3_ROOT_DIR . 'ACP3/';
        $this->classesDir = $this->appDir . 'Core/';
        $this->modulesDir = $this->appDir . 'Modules/';
        $this->uploadsDir = ACP3_ROOT_DIR . 'uploads/';
        $this->cacheDir = ACP3_ROOT_DIR . 'cache/' . $applicationMode . '/';
        $this->designRootPathInternal = ACP3_ROOT_DIR . 'designs/';
    }

    /**
     * @return string
     */
    public function getPhpSelf(): string
    {
        return $this->phpSelf;
    }

    /**
     * @return string
     */
    public function getWebRoot(): string
    {
        return $this->webRoot;
    }

    /**
     * @return string
     */
    public function getAppDir(): string
    {
        return $this->appDir;
    }

    /**
     * @return string
     */
    public function getClassesDir(): string
    {
        return $this->classesDir;
    }

    /**
     * @return string
     */
    public function getModulesDir(): string
    {
        return $this->modulesDir;
    }

    /**
     * @return string
     */
    public function getUploadsDir(): string
    {
        return $this->uploadsDir;
    }

    /**
     * @return string
     */
    public function getCacheDir(): string
    {
        return $this->cacheDir;
    }

    /**
     * @return string
     */
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
}
