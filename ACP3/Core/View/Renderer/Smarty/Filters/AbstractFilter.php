<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Core\View\Renderer\Smarty\Filters;

use ACP3\Core\View\Renderer\Smarty\AbstractPlugin;
use ACP3\Core\View\Renderer\Smarty\PluginInterface;

abstract class AbstractFilter extends AbstractPlugin
{
    /**
     * {@inheritdoc}
     */
    public function getExtensionType()
    {
        return PluginInterface::EXTENSION_TYPE_FILTER;
    }

    /**
     * @param string                    $tplOutput
     * @param \Smarty_Internal_Template $smarty
     *
     * @return string
     */
    abstract public function process($tplOutput, \Smarty_Internal_Template $smarty);
}
