<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Core\View\Renderer\Smarty\Blocks;

class Stylesheets extends AbstractBlock
{
    /**
     * {@inheritdoc}
     */
    public function __invoke(array $params, ?string $content, \Smarty_Internal_Template $smarty, bool &$repeat)
    {
        if (!$repeat && isset($content)) {
            return '@@@SMARTY:STYLESHEETS:BEGIN@@@' . \trim($content) . '@@@SMARTY:STYLESHEETS:END@@@';
        }

        return '';
    }
}
