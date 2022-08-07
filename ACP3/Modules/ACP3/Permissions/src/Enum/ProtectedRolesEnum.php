<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Permissions\Enum;

use ACP3\Core\Enum\EnumValuesTrait;

enum ProtectedRolesEnum: int
{
    use EnumValuesTrait;

    case GUEST = 1;
    case MEMBER = 2;
    case ADMINISTRATOR = 4;
}
