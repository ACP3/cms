<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Core\View\Renderer\Smarty\Functions;

use ACP3\Core;

class IncludeStylesheet extends AbstractFunction
{
    public function __construct(private readonly Core\Assets\IncludeStylesheet $includeStylesheet)
    {
    }

    /**
     * @throws \InvalidArgumentException
     */
    public function __invoke(array $params, \Smarty_Internal_Template $smarty): mixed
    {
        $dependencies = $params['depends'] ?? [];

        if (\is_string($dependencies)) {
            $dependencies = explode(',', $dependencies);
        }

        return $this->includeStylesheet->add($params['module'] ?? '', $params['file'], $dependencies);
    }
}
