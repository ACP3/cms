<?php

namespace ACP3\Core\View\Renderer;

use ACP3\Core\Environment\ApplicationMode;
use ACP3\Core\Environment\ApplicationPath;

/**
 * Renderer for the Smarty template engine
 *
 * @package ACP3\Core\View\Renderer
 */
class Smarty extends AbstractRenderer
{
    /**
     * @var \Smarty
     */
    public $renderer;
    /**
     * @var \ACP3\Core\Environment\ApplicationPath
     */
    protected $appPath;

    /**
     * Smarty constructor.
     *
     * @param \ACP3\Core\Environment\ApplicationPath $appPath
     */
    public function __construct(ApplicationPath $appPath)
    {
        $this->appPath = $appPath;
    }

    /**
     * @param array $params
     *
     * @throws \SmartyException
     */
    public function configure(array $params = [])
    {
        $settings = $this->container->get('core.config')->getSettings('system');

        $this->renderer = new \Smarty();
        $this->renderer->error_reporting = $this->isDevOrInstall() ? E_ALL : 0;
        $this->renderer->compile_id = !empty($params['compile_id']) ? $params['compile_id'] : $settings['design'];
        $this->renderer->compile_check = $this->isDevOrInstall();
        $this->renderer->compile_dir = $this->appPath->getCacheDir() . 'tpl_compiled/';
        $this->renderer->cache_dir = $this->appPath->getCacheDir() . 'tpl_cached/';
    }

    /**
     * @inheritdoc
     */
    public function assign($name, $value = null)
    {
        $this->renderer->assign($name, $value);
    }

    /**
     * @param string      $template
     * @param string|null $cacheId
     * @param string|null $compileId
     * @param string|null $parent
     * @param bool        $display
     *
     * @return bool|mixed|string
     */
    public function fetch($template, $cacheId = null, $compileId = null, $parent = null, $display = false)
    {
        return $this->renderer->fetch($template, $cacheId, $compileId, $parent, $display);
    }

    /**
     * @param string      $template
     * @param string|null $cacheId
     * @param string|null $compileId
     * @param string|null $parent
     */
    public function display($template, $cacheId = null, $compileId = null, $parent = null)
    {
        $this->renderer->display($template, $cacheId, $compileId, $parent);
    }

    /**
     * @inheritdoc
     */
    public function templateExists($template)
    {
        return $this->renderer->templateExists($template);
    }

    /**
     * @return bool
     */
    protected function isDevOrInstall()
    {
        return $this->container->getParameter('core.environment') === ApplicationMode::DEVELOPMENT ||
        $this->container->getParameter('core.environment') === ApplicationMode::INSTALLER ||
        $this->container->getParameter('core.environment') === ApplicationMode::UPDATER;
    }
}
