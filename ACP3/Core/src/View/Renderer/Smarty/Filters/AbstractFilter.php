<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Core\View\Renderer\Smarty\Filters;

use ACP3\Core\View\Renderer\Smarty\PluginInterface;

abstract class AbstractFilter implements PluginInterface
{
    abstract public function __invoke(string $tplOutput, \Smarty_Internal_Template $smarty);
}
