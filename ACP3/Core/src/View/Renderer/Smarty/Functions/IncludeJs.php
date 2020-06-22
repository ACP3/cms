<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Core\View\Renderer\Smarty\Functions;

use ACP3\Core;

class IncludeJs extends AbstractFunction
{
    /**
     * @var \ACP3\Core\Assets\IncludeJs
     */
    private $includeJs;

    /**
     * @param \ACP3\Core\Assets\IncludeJs $includeJs
     */
    public function __construct(Core\Assets\IncludeJs $includeJs)
    {
        $this->includeJs = $includeJs;
    }

    /**
     * {@inheritdoc}
     *
     * @throws \InvalidArgumentException
     */
    public function __invoke(array $params, \Smarty_Internal_Template $smarty)
    {
        $dependencies = $params['depends'] ?? [];

        if (\is_string($dependencies)) {
            $dependencies = \explode(',', $dependencies);
        }

        return $this->includeJs->add($params['module'] ?? '', $params['file'], $dependencies);
    }
}
