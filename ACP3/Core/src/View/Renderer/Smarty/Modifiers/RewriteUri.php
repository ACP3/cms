<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Core\View\Renderer\Smarty\Modifiers;

use ACP3\Core\Helpers\Formatter\RewriteInternalUri;

class RewriteUri extends AbstractModifier
{
    public function __construct(protected RewriteInternalUri $rewriteInternalUri)
    {
    }

    public function __invoke(string $value): string
    {
        return $this->rewriteInternalUri->rewriteInternalUri($value);
    }
}
