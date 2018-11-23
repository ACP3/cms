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
     *
     * @param \Smarty                           $smarty
     * @param \Psr\Container\ContainerInterface $container
     */
    public function __construct(\Smarty $smarty, ContainerInterface $container)
    {
        $this->smarty = $smarty;
        $this->container = $container;
    }

    /**
     * @param string $blockName
     * @param string $serviceId
     *
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
     * @param string $filterName
     * @param string $serviceId
     *
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
     * @param string $pluginName
     * @param string $serviceId
     *
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
     * @param string $pluginName
     * @param string $serviceId
     *
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

    /**
     * @param string                                                     $resourceName
     * @param \ACP3\Core\View\Renderer\Smarty\Resources\AbstractResource $resource
     */
    public function registerResource(string $resourceName, AbstractResource $resource): void
    {
        $this->smarty->registerResource($resourceName, $resource);
    }

    /**
     * {@inheritdoc}
     */
    public function assign($name, $value = null): void
    {
        $this->smarty->assign($name, $value);
    }

    /**
     * {@inheritdoc}
     */
    public function getTemplateVars(?string $variableName = null)
    {
        return $this->smarty->getTemplateVars($variableName);
    }

    /**
     * {@inheritdoc}
     *
     * @throws \SmartyException
     */
    public function fetch(string $template, ?string $cacheId = null, ?string $compileId = null, $parent = null): string
    {
        return $this->smarty->fetch($template, $cacheId, $compileId, $parent);
    }

    /**
     * {@inheritdoc}
     *
     * @throws \SmartyException
     */
    public function display(string $template, ?string $cacheId = null, ?string $compileId = null, $parent = null): void
    {
        $this->smarty->display($template, $cacheId, $compileId, $parent);
    }

    /**
     * {@inheritdoc}
     *
     * @throws \SmartyException
     */
    public function templateExists(string $template): bool
    {
        return $this->smarty->templateExists($template);
    }
}
