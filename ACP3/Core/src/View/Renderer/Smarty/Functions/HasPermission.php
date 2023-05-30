<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Core\View\Renderer\Smarty\Functions;

use ACP3\Core;

class HasPermission extends AbstractFunction
{
    public function __construct(protected Core\ACL $acl)
    {
    }

    public function __invoke(array $params, \Smarty_Internal_Template $smarty): bool
    {
        if (isset($params['path']) === true) {
            return $this->acl->hasPermission($params['path']);
        }

        return false;
    }
}
