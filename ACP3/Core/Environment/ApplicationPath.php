<?php
namespace ACP3\Core\Environment;

/**
 * Class ApplicationPath
 * @package ACP3\Core\Environment
 */
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
    protected $designPathInternal;
    /**
     * @var string
     */
    protected $designPathWeb;
    /**
     * @var string
     */
    protected $designPathAbsolute;

    /**
     * ApplicationPath constructor.
     *
     * @param string $environment
     */
    public function __construct($environment)
    {
        $this->phpSelf = htmlentities($_SERVER['SCRIPT_NAME']);
        $this->webRoot = substr($this->phpSelf, 0, strrpos($this->phpSelf, '/') + 1);
        $this->appDir = ACP3_ROOT_DIR . 'ACP3/';
        $this->classesDir = $this->appDir . 'Core/';
        $this->modulesDir = $this->appDir . 'Modules/';
        $this->uploadsDir = ACP3_ROOT_DIR . 'uploads/';
        $this->cacheDir = ACP3_ROOT_DIR . 'cache/' . $environment . '/';
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
        $this->designPathInternal = $designPathInternal;
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
        $this->designPathAbsolute = $designPathAbsolute;
        return $this;
    }
}
