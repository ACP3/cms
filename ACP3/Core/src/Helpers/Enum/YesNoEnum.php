<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Core\Helpers\Enum;

use ACP3\Core\Enum\EnumValuesTrait;

enum YesNoEnum: int
{
    use EnumValuesTrait;

    case NO = 0;
    case YES = 1;
}
