<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Core\View\Renderer\Smarty\Functions;

use ACP3\Core;

class Uri extends AbstractFunction
{
    /**
     * @var \ACP3\Core\Router\RouterInterface
     */
    private $router;

    public function __construct(Core\Router\RouterInterface $router)
    {
        $this->router = $router;
    }

    /**
     * {@inheritdoc}
     */
    public function __invoke(array $params, \Smarty_Internal_Template $smarty)
    {
        return $this->router->route(
            !empty($params['args']) ? $params['args'] : '',
            isset($params['absolute']) && (bool) $params['absolute'],
            isset($params['secure']) ? (bool) $params['secure'] : null
        );
    }
}
