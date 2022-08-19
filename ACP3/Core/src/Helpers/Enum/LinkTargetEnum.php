<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Core\Helpers\Enum;

use ACP3\Core\Enum\EnumValuesTrait;

enum LinkTargetEnum: int
{
    use EnumValuesTrait;

    case TARGET_SELF = 1;
    case TARGET_BLANK = 2;
}
