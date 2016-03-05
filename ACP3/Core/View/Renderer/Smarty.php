<?php

namespace ACP3\Core\View\Renderer;

use ACP3\Core\Config;
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
     * @var \ACP3\Core\Config
     */
    protected $config;
    /**
     * @var string
     */
    protected $environment;

    /**
     * Smarty constructor.
     *
     * @param \ACP3\Core\Environment\ApplicationPath $appPath
     * @param \ACP3\Core\Config                      $config
     * @param string                                 $environment
     */
    public function __construct(
        ApplicationPath $appPath,
        Config $config,
        $environment
    ) {
        parent::__construct();

        $this->config = $config;
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
        $settings = $this->config->getSettings('system');

        $this->setErrorReporting($this->isDevOrInstall() ? E_ALL : 0);
        $this->setCompileId(!empty($params['compile_id']) ? $params['compile_id'] : $settings['design']);
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
