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
        if (is_array($name)) {
            $this->renderer->assign($name);
        } else {
            $this->renderer->assign($name, $value);
        }
    }

    /**
     * @param      $template
     * @param null $cache_id
     * @param null $compile_id
     * @param null $parent
     * @param bool $display
     *
     * @return bool|mixed|string
     */
    public function fetch($template, $cache_id = null, $compile_id = null, $parent = null, $display = false)
    {
        return $this->renderer->fetch($template, $cache_id, $compile_id, $parent, $display);
    }

    /**
     * @param      $template
     * @param null $cache_id
     * @param null $compile_id
     * @param null $parent
     */
    public function display($template, $cache_id = null, $compile_id = null, $parent = null)
    {
        $this->renderer->display($template, $cache_id, $compile_id, $parent);
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
