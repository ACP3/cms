<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Core\View\Renderer\Smarty\Blocks;

use ACP3\Core\View\Renderer\Smarty\AbstractPlugin;
use ACP3\Core\View\Renderer\Smarty\PluginInterface;

abstract class AbstractBlock extends AbstractPlugin
{
    /**
     * @inheritdoc
     */
    public function getExtensionType()
    {
        return PluginInterface::EXTENSION_TYPE_BLOCK;
    }

    /**
     * @param                           $params
     * @param                           $content
     * @param \Smarty_Internal_Template $smarty
     * @param                           $repeat
     *
     * @return string
     */
    abstract public function process($params, $content, \Smarty_Internal_Template $smarty, &$repeat);
}
