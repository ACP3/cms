<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Seo\Enum;

use ACP3\Core\Enum\EnumValuesTrait;

enum MetaRobotsEnum: int
{
    use EnumValuesTrait;

    case INDEX_FOLLOW = 1;
    case INDEX_NOFOLLOW = 2;
    case NOINDEX_FOLLOW = 3;
    case NOINDEX_NOFOLLOW = 4;
}
