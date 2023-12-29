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
    public function __construct(private readonly \Smarty $smarty, private readonly ContainerInterface $container)
    {
    }

    /**
     * @throws \SmartyException
     */
    public function registerBlock(string $blockName, string $serviceId): void
    {
        $this->smarty->registerPlugin(
            PluginTypeEnum::BLOCK->value,
            $blockName,
            fn (array $params, ?string $content, \Smarty_Internal_Template $smarty, bool &$repeat) => $this->container->get($serviceId)($params, $content, $smarty, $repeat)
        );
    }

    /**
     * @throws \SmartyException
     */
    public function registerFilter(string $filterName, string $serviceId): void
    {
        $this->smarty->registerFilter(
            $filterName,
            fn ($tplOutput, \Smarty_Internal_Template $smarty) => $this->container->get($serviceId)($tplOutput, $smarty),
            $serviceId
        );
    }

    /**
     * @throws \SmartyException
     */
    public function registerFunction(string $pluginName, string $serviceId): void
    {
        $this->smarty->registerPlugin(
            PluginTypeEnum::FUNCTION->value,
            $pluginName,
            fn (array $params, \Smarty_Internal_Template $smarty) => $this->container->get($serviceId)($params, $smarty)
        );
    }

    /**
     * @throws \SmartyException
     */
    public function registerModifier(string $pluginName, string $serviceId): void
    {
        $this->smarty->registerPlugin(
            PluginTypeEnum::MODIFIER->value,
            $pluginName,
            fn (...$value) => $this->container->get($serviceId)(...$value)
        );
    }

    public function registerResource(string $resourceName, AbstractResource $resource): void
    {
        $this->smarty->registerResource($resourceName, $resource);
    }

    public function assign($name, mixed $value = null): RendererInterface
    {
        $this->smarty->assign($name, $value);

        return $this;
    }

    public function getTemplateVars(string $variableName = null): mixed
    {
        return $this->smarty->getTemplateVars($variableName);
    }

    /**
     * @throws \SmartyException
     */
    public function fetch(string $template, string $cacheId = null, string $compileId = null, object $parent = null): string
    {
        return $this->smarty->fetch($template, $cacheId, $compileId, $parent);
    }

    /**
     * @throws \SmartyException
     */
    public function display(string $template, string $cacheId = null, string $compileId = null, object $parent = null): void
    {
        $this->smarty->display($template, $cacheId, $compileId, $parent);
    }

    /**
     * @throws \SmartyException
     */
    public function templateExists(string $template): bool
    {
        return $this->smarty->templateExists($template);
    }
}
