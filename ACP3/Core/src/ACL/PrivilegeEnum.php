<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Core\ACL;

enum PrivilegeEnum: int
{
    case ADMIN_SETTINGS = 7;
    case ADMIN_DELETE = 6;
    case ADMIN_EDIT = 5;
    case ADMIN_CREATE = 4;
    case ADMIN_VIEW = 3;
    case FRONTEND_CREATE = 2;
    case FRONTEND_VIEW = 1;
}
