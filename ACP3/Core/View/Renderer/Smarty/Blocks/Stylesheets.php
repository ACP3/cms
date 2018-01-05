<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Core\View\Renderer\Smarty\Blocks;

class Stylesheets extends AbstractBlock
{
    /**
     * @inheritdoc
     */
    public function getExtensionName()
    {
        return 'stylesheets';
    }

    /**
     * @param                           $params
     * @param                           $content
     * @param \Smarty_Internal_Template $smarty
     * @param                           $repeat
     *
     * @return string
     */
    public function process($params, $content, \Smarty_Internal_Template $smarty, &$repeat)
    {
        if (!$repeat) {
            if (isset($content)) {
                return '@@@SMARTY:STYLESHEETS:BEGIN@@@' . \trim($content) . '@@@SMARTY:STYLESHEETS:END@@@';
            }
        }

        return '';
    }
}
