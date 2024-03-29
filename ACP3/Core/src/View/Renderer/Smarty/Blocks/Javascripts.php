<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Core\View\Renderer\Smarty\Blocks;

class Javascripts extends AbstractBlock
{
    public function __invoke(array $params, ?string $content, \Smarty_Internal_Template $smarty, bool &$repeat): string
    {
        if (!$repeat && isset($content)) {
            return '@@@SMARTY:JAVASCRIPTS:BEGIN@@@' . trim($content) . '@@@SMARTY:JAVASCRIPTS:END@@@';
        }

        return '';
    }
}
