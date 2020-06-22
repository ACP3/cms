<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Core\View\Renderer\Smarty\Blocks;

use ACP3\Core\View\Renderer\Smarty\PluginInterface;

abstract class AbstractBlock implements PluginInterface
{
    /**
     * @return string
     */
    abstract public function __invoke(array $params, ?string $content, \Smarty_Internal_Template $smarty, bool &$repeat);
}
