<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Core\View\Renderer\Smarty\Blocks;

abstract class AbstractBlock
{
    /**
     * @param array                     $params
     * @param string|null               $content
     * @param \Smarty_Internal_Template $smarty
     * @param bool                      $repeat
     *
     * @return string
     */
    abstract public function __invoke(array $params, ?string $content, \Smarty_Internal_Template $smarty, bool &$repeat);
}
