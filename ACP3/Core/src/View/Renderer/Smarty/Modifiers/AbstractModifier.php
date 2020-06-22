<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Core\View\Renderer\Smarty\Modifiers;

use ACP3\Core\View\Renderer\Smarty\PluginInterface;

abstract class AbstractModifier implements PluginInterface
{
    /**
     * @param string $value
     */
    abstract public function __invoke($value): string;
}
