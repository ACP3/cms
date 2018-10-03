<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Core\View\Renderer\Smarty\Blocks;

use ACP3\Core\View\Renderer\Smarty\PluginInterface;
use ACP3\Core\View\Renderer\Smarty\PluginTypeEnum;

abstract class AbstractBlock implements PluginInterface
{
    /**
     * {@inheritdoc}
     */
    public function getExtensionType()
    {
        return PluginTypeEnum::BLOCK;
    }

    /**
     * {@inheritdoc}
     *
     * @throws \SmartyException
     */
    public function register(\Smarty $smarty)
    {
        $smarty->registerPlugin(PluginTypeEnum::BLOCK, $this->getExtensionName(), [$this, '__invoke']);
    }

    /**
     * @param array                     $params
     * @param string|null               $content
     * @param \Smarty_Internal_Template $smarty
     * @param bool                      $repeat
     *
     * @return string
     */
    public function __invoke(array $params, ?string $content, \Smarty_Internal_Template $smarty, bool &$repeat)
    {
        return $this->process($params, $content, $smarty, $repeat);
    }

    /**
     * @param array                     $params
     * @param string|null               $content
     * @param \Smarty_Internal_Template $smarty
     * @param bool                      $repeat
     *
     * @return string
     *
     * @deprecated since version 4.30.0, to be remove with 5.0.0. Implement method __invoke() instead
     */
    abstract public function process($params, $content, \Smarty_Internal_Template $smarty, &$repeat);
}
