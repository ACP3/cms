<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Core\View\Renderer;

use ACP3\Core\View\Renderer\Smarty\PluginInterface;

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
     * @var array
     */
    private static $pluginTypesMap = [
        PluginInterface::EXTENSION_TYPE_BLOCK,
        PluginInterface::EXTENSION_TYPE_FUNCTION,
        PluginInterface::EXTENSION_TYPE_MODIFIER,
    ];

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
     *
     * @throws \SmartyException
     */
    public function registerSmartyPlugin(Smarty\PluginInterface $plugin)
    {
        if ($this->isPlugin($plugin)) {
            $this->smarty->registerPlugin(
                $plugin::getExtensionType(),
                $plugin::getExtensionName(),
                [$plugin, 'process']
            );
        } elseif ($plugin::getExtensionType() === PluginInterface::EXTENSION_TYPE_FILTER) {
            $this->smarty->registerFilter($plugin::getExtensionName(), [$plugin, 'process']);
        } elseif ($plugin::getExtensionType() === PluginInterface::EXTENSION_TYPE_RESOURCE) {
            $this->smarty->registerResource($plugin::getExtensionName(), $plugin);
        }
    }

    /**
     * @param \ACP3\Core\View\Renderer\Smarty\PluginInterface $plugin
     *
     * @return bool
     */
    private function isPlugin(Smarty\PluginInterface $plugin): bool
    {
        return \in_array(
            $plugin::getExtensionType(),
            self::$pluginTypesMap
        );
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
