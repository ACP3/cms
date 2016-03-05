<?php

namespace ACP3\Core\View\Renderer;

use ACP3\Core\Environment\ApplicationMode;
use ACP3\Core\Environment\ApplicationPath;

/**
 * Renderer for the Smarty template engine
 *
 * @package ACP3\Core\View\Renderer
 */
class Smarty extends \Smarty implements RendererInterface
{
    /**
     * @var \ACP3\Core\Environment\ApplicationPath
     */
    protected $appPath;
    /**
     * @var string
     */
    protected $environment;

    /**
     * Smarty constructor.
     *
     * @param \ACP3\Core\Environment\ApplicationPath $appPath
     * @param string                                 $environment
     */
    public function __construct(ApplicationPath $appPath, $environment)
    {
        parent::__construct();

        $this->appPath = $appPath;
        $this->environment = $environment;
    }

    /**
     * @param array $params
     *
     * @throws \SmartyException
     */
    public function configure(array $params = [])
    {
        $this->setErrorReporting($this->isDevOrInstall() ? E_ALL : 0);
        $this->setCompileId(!empty($params['compile_id']) ? $params['compile_id'] : $this->environment);
        $this->setCompileCheck($this->isDevOrInstall());
        $this->setCompileDir($this->appPath->getCacheDir() . 'tpl_compiled/');
        $this->setCacheDir($this->appPath->getCacheDir() . 'tpl_cached/');
    }

    /**
     * @return bool
     */
    protected function isDevOrInstall()
    {
        $environments = [ApplicationMode::DEVELOPMENT, ApplicationMode::INSTALLER, ApplicationMode::UPDATER];
        return in_array($this->environment, $environments);
    }
}
