<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Core\View\Renderer;

/**
 * Renderer for the Smarty template engine.
 */
class Smarty implements RendererInterface
{
    /**
     * @var \Smarty
     */
    protected $smarty;

    /**
     * Smarty constructor.
     *
     * @param \Smarty $smarty
     */
    public function __construct(\Smarty $smarty)
    {
        $this->smarty = $smarty;
    }

    /**
     * @param \ACP3\Core\View\Renderer\Smarty\PluginInterface $plugin
     */
    public function registerSmartyPlugin(Smarty\PluginInterface $plugin)
    {
        $plugin->register($this->smarty);
    }

    /**
     * {@inheritdoc}
     */
    public function assign($name, $value = null)
    {
        $this->smarty->assign($name, $value);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getTemplateVars($variableName = null)
    {
        return $this->smarty->getTemplateVars($variableName);
    }

    /**
     * {@inheritdoc}
     *
     * @throws \SmartyException
     */
    public function fetch($template, $cacheId = null, $compileId = null, $parent = null)
    {
        return $this->smarty->fetch($template, $cacheId, $compileId, $parent);
    }

    /**
     * {@inheritdoc}
     *
     * @throws \SmartyException
     */
    public function display($template, $cacheId = null, $compileId = null, $parent = null)
    {
        return $this->smarty->display($template, $cacheId, $compileId, $parent);
    }

    /**
     * {@inheritdoc}
     *
     * @throws \SmartyException
     */
    public function templateExists($template)
    {
        return $this->smarty->templateExists($template);
    }
}
