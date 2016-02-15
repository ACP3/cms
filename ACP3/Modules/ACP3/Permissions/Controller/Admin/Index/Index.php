<?php
/**
 * Copyright (c) 2016 by the ACP3 Developers. See the LICENCE file at the top-level module directory for licencing details.
 */

namespace ACP3\Modules\ACP3\Permissions\Controller\Admin\Index;

use ACP3\Core;
use ACP3\Modules\ACP3\Permissions;

/**
 * Class Index
 * @package ACP3\Modules\ACP3\Permissions\Controller\Admin\Index
 */
class Index extends Core\Modules\AdminController
{
    public function execute()
    {
        $roles = $this->acl->getAllRoles();
        $c_roles = count($roles);

        if ($c_roles > 0) {
            for ($i = 0; $i < $c_roles; ++$i) {
                $roles[$i]['spaces'] = str_repeat('&nbsp;&nbsp;', $roles[$i]['level']);
            }
            $this->view->assign('roles', $roles);
            $this->view->assign('can_delete', $this->acl->hasPermission('admin/permissions/index/delete'));
            $this->view->assign('can_order', $this->acl->hasPermission('admin/permissions/index/order'));
        }
    }
}
