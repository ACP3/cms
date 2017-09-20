<?php
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
    private $designPathInternal;
    /**
     * @var string
     */
    private $designPathWeb;
    /**
     * @var string
     */
    private $designPathAbsolute;
    /**
     * @var string
     */
    private $environment;

    /**
     * ApplicationPath constructor.
     *
     * @param string $applicationMode
     */
    public function __construct(string $applicationMode)
    {
        $this->environment = $applicationMode;
        $this->phpSelf = htmlentities($_SERVER['SCRIPT_NAME']);
        $this->webRoot = substr($this->phpSelf, 0, strrpos($this->phpSelf, '/') + 1);
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
    public function getEnvironment(): string
    {
        return $this->environment;
    }

    /**
     * @return string
     */
    public function getPhpSelf()
    {
        return $this->phpSelf;
    }

    /**
     * @param string $phpSelf
     *
     * @return ApplicationPath
     */
    public function setPhpSelf($phpSelf)
    {
        $this->phpSelf = $phpSelf;
        return $this;
    }

    /**
     * @return string
     */
    public function getWebRoot()
    {
        return $this->webRoot;
    }

    /**
     * @param string $webRoot
     *
     * @return ApplicationPath
     */
    public function setWebRoot($webRoot)
    {
        $this->webRoot = $webRoot;
        return $this;
    }

    /**
     * @return string
     */
    public function getAppDir()
    {
        return $this->appDir;
    }

    /**
     * @param string $appDir
     *
     * @return ApplicationPath
     */
    public function setAppDir($appDir)
    {
        $this->appDir = $appDir;
        return $this;
    }

    /**
     * @return string
     */
    public function getClassesDir()
    {
        return $this->classesDir;
    }

    /**
     * @param string $classesDir
     *
     * @return ApplicationPath
     */
    public function setClassesDir($classesDir)
    {
        $this->classesDir = $classesDir;
        return $this;
    }

    /**
     * @return string
     */
    public function getModulesDir()
    {
        return $this->modulesDir;
    }

    /**
     * @param string $modulesDir
     *
     * @return ApplicationPath
     */
    public function setModulesDir($modulesDir)
    {
        $this->modulesDir = $modulesDir;
        return $this;
    }

    /**
     * @return string
     */
    public function getUploadsDir()
    {
        return $this->uploadsDir;
    }

    /**
     * @param string $uploadsDir
     *
     * @return ApplicationPath
     */
    public function setUploadsDir($uploadsDir)
    {
        $this->uploadsDir = $uploadsDir;
        return $this;
    }

    /**
     * @return string
     */
    public function getCacheDir()
    {
        return $this->cacheDir;
    }

    /**
     * @param string $cacheDir
     *
     * @return ApplicationPath
     */
    public function setCacheDir($cacheDir)
    {
        $this->cacheDir = $cacheDir;
        return $this;
    }

    /**
     * @return string
     */
    public function getDesignRootPathInternal()
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

    /**
     * @return string
     */
    public function getDesignPathInternal()
    {
        return $this->designPathInternal;
    }

    /**
     * @param string $designPathInternal
     *
     * @return ApplicationPath
     */
    public function setDesignPathInternal($designPathInternal)
    {
        $this->designPathInternal = $this->designRootPathInternal . $designPathInternal;
        return $this;
    }

    /**
     * @return string
     */
    public function getDesignPathWeb()
    {
        return $this->designPathWeb;
    }

    /**
     * @param string $designPathWeb
     *
     * @return ApplicationPath
     */
    public function setDesignPathWeb($designPathWeb)
    {
        $this->designPathWeb = $designPathWeb;
        return $this;
    }

    /**
     * @return string
     */
    public function getDesignPathAbsolute()
    {
        return $this->designPathAbsolute;
    }

    /**
     * @param string $designPathAbsolute
     *
     * @return ApplicationPath
     */
    public function setDesignPathAbsolute($designPathAbsolute)
    {
        if (!preg_match('=^(http(s?))://=', $designPathAbsolute)) {
            throw new \InvalidArgumentException('The given absolute design path (' . $designPathAbsolute . ') doesn\'t start with a valid protocol.');
        }

        $this->designPathAbsolute = $designPathAbsolute;
        return $this;
    }
}
