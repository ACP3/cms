<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Core\ACL;

enum PermissionEnum: int
{
    case DENY_ACCESS = 0;
    case PERMIT_ACCESS = 1;
    case INHERIT_ACCESS = 2;
}
