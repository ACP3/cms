<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Core\View\Renderer;

use ACP3\Core\View\Renderer\Smarty\PluginTypeEnum;
use ACP3\Core\View\Renderer\Smarty\Resources\AbstractResource;
use Psr\Container\ContainerInterface;

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
     * @var \Psr\Container\ContainerInterface
     */
    private $container;

    /**
     * Smarty constructor.
     */
    public function __construct(\Smarty $smarty, ContainerInterface $container)
    {
        $this->smarty = $smarty;
        $this->container = $container;
    }

    /**
     * @param \ACP3\Core\View\Renderer\Smarty\PluginInterface $plugin
     *
     * @deprecated since version 4.33.0, to be removed with version 5.0.0. Use other ::register*() methods instead
     */
    public function registerSmartyPlugin(Smarty\PluginInterface $plugin)
    {
        $plugin->register($this->smarty);
    }

    /**
     * @throws \SmartyException
     */
    public function registerBlock(string $blockName, string $serviceId): void
    {
        $this->smarty->registerPlugin(
            PluginTypeEnum::BLOCK,
            $blockName,
            function (array $params, ?string $content, \Smarty_Internal_Template $smarty, bool &$repeat) use ($serviceId) {
                return $this->container->get($serviceId)($params, $content, $smarty, $repeat);
            }
        );
    }

    /**
     * @throws \SmartyException
     */
    public function registerFilter(string $filterName, string $serviceId): void
    {
        $this->smarty->registerFilter(
            $filterName,
            function ($tplOutput, \Smarty_Internal_Template $smarty) use ($serviceId) {
                return $this->container->get($serviceId)($tplOutput, $smarty);
            },
            $serviceId
        );
    }

    /**
     * @throws \SmartyException
     */
    public function registerFunction(string $pluginName, string $serviceId): void
    {
        $this->smarty->registerPlugin(
            PluginTypeEnum::FUNCTION,
            $pluginName,
            function (array $params, \Smarty_Internal_Template $smarty) use ($serviceId) {
                return $this->container->get($serviceId)($params, $smarty);
            }
        );
    }

    /**
     * @throws \SmartyException
     */
    public function registerModifier(string $pluginName, string $serviceId): void
    {
        $this->smarty->registerPlugin(
            PluginTypeEnum::MODIFIER,
            $pluginName,
            function ($value) use ($serviceId) {
                return $this->container->get($serviceId)($value);
            }
        );
    }

    public function registerResource(string $resourceName, AbstractResource $resource): void
    {
        $this->smarty->registerResource($resourceName, $resource);
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
