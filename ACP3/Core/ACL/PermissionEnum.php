<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Core\ACL;

use ACP3\Core\Enum\BaseEnum;

class PermissionEnum extends BaseEnum
{
    const DENY_ACCESS = 0;
    const PERMIT_ACCESS = 1;
    const INHERIT_ACCESS = 2;
}
