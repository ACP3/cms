<?php
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
     * @param $appMode
     */
    public function __construct($appMode)
    {
        parent::__construct($appMode);

        $this->installerWebRoot = $this->webRoot;
        $this->webRoot = substr($this->webRoot !== '/' ? $this->webRoot . '/' : '/', 0, -14);
        $this->installerAppDir = realpath($this->appDir . '../installation') . '/Installer/';
        $this->installerModulesDir = $this->installerAppDir . 'Modules/';
        $this->installerClassesDir = $this->installerAppDir . 'Core/';
    }

    /**
     * @return string
     */
    public function getInstallerAppDir()
    {
        return $this->installerAppDir;
    }

    /**
     * @param string $installerAppDir
     *
     * @return ApplicationPath
     */
    public function setInstallerAppDir($installerAppDir)
    {
        $this->installerAppDir = $installerAppDir;
        return $this;
    }

    /**
     * @return string
     */
    public function getInstallerCacheDir()
    {
        return $this->installerCacheDir;
    }

    /**
     * @param string $installerCacheDir
     *
     * @return ApplicationPath
     */
    public function setInstallerCacheDir($installerCacheDir)
    {
        $this->installerCacheDir = $installerCacheDir;
        return $this;
    }

    /**
     * @return string
     */
    public function getInstallerClassesDir()
    {
        return $this->installerClassesDir;
    }

    /**
     * @param string $installerClassesDir
     *
     * @return ApplicationPath
     */
    public function setInstallerClassesDir($installerClassesDir)
    {
        $this->installerClassesDir = $installerClassesDir;
        return $this;
    }

    /**
     * @return string
     */
    public function getInstallerModulesDir()
    {
        return $this->installerModulesDir;
    }

    /**
     * @param string $installerModulesDir
     *
     * @return ApplicationPath
     */
    public function setInstallerModulesDir($installerModulesDir)
    {
        $this->installerModulesDir = $installerModulesDir;
        return $this;
    }

    /**
     * @return string
     */
    public function getInstallerWebRoot()
    {
        return $this->installerWebRoot;
    }

    /**
     * @param string $installerWebRoot
     *
     * @return ApplicationPath
     */
    public function setInstallerWebRoot($installerWebRoot)
    {
        $this->installerWebRoot = $installerWebRoot;
        return $this;
    }
}