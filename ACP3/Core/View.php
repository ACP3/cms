<?php

namespace ACP3\Core;

use ACP3\Core\View\Renderer\RendererInterface;
use ACP3\Core\View\Renderer\Smarty;

/**
 * Klasse für die Ausgabe der Seite
 * @package ACP3\Core
 */
class View
{
    /**
     * @var RendererInterface
     */
    protected $renderer;

    /**
     * Gets the renderer
     *
     * @return RendererInterface
     */
    public function getRenderer()
    {
        return $this->renderer;
    }

    /**
     * @param \ACP3\Core\View\Renderer\Smarty $smarty
     * @param array                           $params
     */
    public function __construct(Smarty $smarty, array $params = [])
    {
        $this->renderer = $smarty;
        $this->renderer->configure($params);
    }

    /**
     * Fetches a template and outputs its contents
     *
     * @param      string $template
     * @param null        $cacheId
     * @param null        $compileId
     * @param null        $parent
     */
    public function displayTemplate($template, $cacheId = null, $compileId = null, $parent = null)
    {
        echo $this->fetchTemplate($template, $cacheId, $compileId, $parent, true);
    }

    /**
     * Fetches a template and returns its contents
     *
     * @param string  $template
     * @param mixed   $cacheId
     * @param mixed   $compileId
     * @param object  $parent
     * @param boolean $display
     *
     * @throws \Exception
     * @return string
     */
    public function fetchTemplate($template, $cacheId = null, $compileId = null, $parent = null, $display = false)
    {
        return $this->renderer->fetch('asset:' . $template, $cacheId, $compileId, $parent, $display);
    }

    /**
     * Checks, whether a template exists or not
     *
     * @param string $template
     *
     * @return boolean
     */
    public function templateExists($template)
    {
        return $this->renderer->templateExists($template);
    }

    /**
     * Assigns a new template variable
     *
     * @param string|array $name
     * @param mixed        $value
     *
     * @return boolean
     */
    public function assign($name, $value = null)
    {
        return $this->renderer->assign($name, $value);
    }

    /**
     * @param \ACP3\Core\View\Renderer\Smarty\PluginInterface $plugin
     */
    public function registerPlugin(Smarty\PluginInterface $plugin)
    {
        $plugin->register($this->renderer);
    }
}
