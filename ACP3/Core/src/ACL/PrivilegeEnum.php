<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Core\ACL;

use ACP3\Core\Enum\BaseEnum;

class PrivilegeEnum extends BaseEnum
{
    const ADMIN_SETTINGS = 7;
    const ADMIN_DELETE = 6;
    const ADMIN_EDIT = 5;
    const ADMIN_CREATE = 4;
    const ADMIN_VIEW = 3;
    const FRONTEND_CREATE = 2;
    const FRONTEND_VIEW = 1;
}
