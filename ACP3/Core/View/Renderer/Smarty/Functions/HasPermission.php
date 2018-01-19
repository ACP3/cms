<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Core\View\Renderer\Smarty\Functions;

use ACP3\Core;

class HasPermission extends AbstractFunction
{
    /**
     * @var \ACP3\Core\ACL
     */
    protected $acl;

    /**
     * @param \ACP3\Core\ACL $acl
     */
    public function __construct(Core\ACL $acl)
    {
        $this->acl = $acl;
    }

    /**
     * {@inheritdoc}
     */
    public function getExtensionName()
    {
        return 'has_permission';
    }

    /**
     * {@inheritdoc}
     */
    public function process(array $params, \Smarty_Internal_Template $smarty)
    {
        if (isset($params['path']) === true) {
            return $this->acl->hasPermission($params['path']);
        }

        return false;
    }
}
