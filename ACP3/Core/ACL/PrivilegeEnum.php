<?php
namespace ACP3\Core\ACL;

use ACP3\Core\Enum\BaseEnum;

/**
 * Class PrivilegeEnum
 * @package ACP3\Core\ACL
 */
class PrivilegeEnum extends BaseEnum
{
    const ADMIN_SETTINGS = 7;
    const ADMIN_DELETE = 6;
    const ADMIN_MANAGE = 8;
    const ADMIN_EDIT = 5;
    const ADMIN_CREATE = 4;
    const ADMIN_VIEW = 3;
    const FRONTEND_CREATE = 2;
    const FRONTEND_VIEW = 1;
}
