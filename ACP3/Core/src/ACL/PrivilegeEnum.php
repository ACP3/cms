<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Core\ACL;

use ACP3\Core\Enum\BaseEnum;

class PrivilegeEnum extends BaseEnum
{
    public const ADMIN_SETTINGS = 7;
    public const ADMIN_DELETE = 6;
    public const ADMIN_EDIT = 5;
    public const ADMIN_CREATE = 4;
    public const ADMIN_VIEW = 3;
    public const FRONTEND_CREATE = 2;
    public const FRONTEND_VIEW = 1;
}
