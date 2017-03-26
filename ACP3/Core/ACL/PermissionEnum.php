<?php
namespace ACP3\Core\ACL;

use ACP3\Core\Enum\BaseEnum;

/**
 * Class PermissionEnum
 * @package ACP3\Core\ACL
 */
class PermissionEnum extends BaseEnum
{
    const DENY_ACCESS = 0;
    const PERMIT_ACCESS = 1;
    const INHERIT_ACCESS = 2;
}
