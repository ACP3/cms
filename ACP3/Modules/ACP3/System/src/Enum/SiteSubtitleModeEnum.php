<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\System\Enum;

use ACP3\Core\Enum\EnumValuesTrait;

enum SiteSubtitleModeEnum: int
{
    use EnumValuesTrait;

    case ALWAYS = 1;
    case HOMEPAGE_ONLY = 2;
    case NEVER = 3;
}
