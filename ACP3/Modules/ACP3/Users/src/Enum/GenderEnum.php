<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Users\Enum;

use ACP3\Core\Enum\EnumValuesTrait;

enum GenderEnum: int
{
    use EnumValuesTrait;

    case NOT_SPECIFIED = 1;
    case FEMALE = 2;
    case MALE = 3;
}
