<?php

namespace ACP3\Core\View\Renderer;

use ACP3\Core\Environment\ApplicationMode;
use ACP3\Core\Environment\ApplicationPath;

/**
 * Renderer for the Smarty template engine
 *
 * @package ACP3\Core\View\Renderer
 */
class Smarty implements RendererInterface
{
    /**
     * @var \Smarty
     */
    protected $smarty;
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
     * @param \Smarty $smarty
     * @param \ACP3\Core\Environment\ApplicationPath $appPath
     * @param string $environment
     */
    public function __construct(
        \Smarty $smarty,
        ApplicationPath $appPath,
        $environment)
    {
        $this->smarty = $smarty;
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
        $this->smarty->setErrorReporting($this->isDevOrInstall() ? E_ALL : 0);
        $this->smarty->setCompileId(!empty($params['compile_id']) ? $params['compile_id'] : $this->environment);
        $this->smarty->setCompileCheck($this->isDevOrInstall());
        $this->smarty->setCompileDir($this->appPath->getCacheDir() . 'tpl_compiled/');
        $this->smarty->setCacheDir($this->appPath->getCacheDir() . 'tpl_cached/');
    }

    /**
     * @return bool
     */
    protected function isDevOrInstall()
    {
        $environments = [ApplicationMode::DEVELOPMENT, ApplicationMode::INSTALLER, ApplicationMode::UPDATER];
        return in_array($this->environment, $environments);
    }

    /**
     * @param \ACP3\Core\View\Renderer\Smarty\PluginInterface $plugin
     */
    public function registerSmartyPlugin(Smarty\PluginInterface $plugin)
    {
        $plugin->register($this->smarty);
    }

    /**
     * @inheritdoc
     */
    public function assign($name, $value = null)
    {
        $this->smarty->assign($name, $value);

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function fetch($template, $cacheId = null, $compileId = null, $parent = null)
    {
        return $this->smarty->fetch($template, $cacheId, $compileId, $parent);
    }

    /**
     * @inheritdoc
     */
    public function display($template, $cacheId = null, $compileId = null, $parent = null)
    {
        return $this->smarty->display($template, $cacheId, $compileId, $parent);
    }

    /**
     * @inheritdoc
     */
    public function templateExists($template)
    {
        return $this->smarty->templateExists($template);
    }
}
