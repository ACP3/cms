<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Core\View\Renderer\Smarty\Functions;

use ACP3\Core\View\Renderer\Smarty\AbstractPlugin;
use ACP3\Core\View\Renderer\Smarty\PluginInterface;

abstract class AbstractFunction extends AbstractPlugin
{
    /**
     * @return string
     */
    public function getExtensionType()
    {
        return PluginInterface::EXTENSION_TYPE_FUNCTION;
    }

    /**
     * @param array                     $params
     * @param \Smarty_Internal_Template $smarty
     *
     * @return mixed
     */
    abstract public function process(array $params, \Smarty_Internal_Template $smarty);
}
