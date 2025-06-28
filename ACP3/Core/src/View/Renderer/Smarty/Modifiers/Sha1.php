<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Core\View\Renderer\Smarty\Modifiers;

class Sha1 extends AbstractModifier
{
    public function __invoke(mixed $value): string
    {
        return hash('sha1', $value);
    }
}
