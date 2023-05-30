<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Core\View\Renderer\Smarty\Modifiers;

class PrefixUri extends AbstractModifier
{
    public function __invoke(string $value): string
    {
        if (!empty($value) && (bool) preg_match('=^http(s)?://=', $value) === false) {
            return 'http://' . $value;
        }

        return $value;
    }
}
