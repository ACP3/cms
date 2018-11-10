<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Core\View\Renderer\Smarty\Modifiers;

abstract class AbstractModifier
{
    /**
     * @param string $value
     *
     * @return string
     */
    abstract public function __invoke($value): string;
}
