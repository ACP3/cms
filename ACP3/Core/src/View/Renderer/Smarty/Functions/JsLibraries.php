<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Core\View\Renderer\Smarty\Functions;

use ACP3\Core\Assets\Libraries;

class JsLibraries extends AbstractFunction
{
    public function __construct(private Libraries $libraries)
    {
    }

    /**
     * {@inheritdoc}
     */
    public function __invoke(array $params, \Smarty_Internal_Template $smarty): string
    {
        $this->libraries->enableLibraries(explode(',', $params['enable']));

        return '';
    }
}
