<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Menus\Enum;

use ACP3\Core\Enum\EnumValuesTrait;

enum PageTypeEnum: int
{
    use EnumValuesTrait;

    case MODULE = 1;
    case DYNAMIC_PAGE = 2;
    case HYPERLINK = 3;
    case HEADLINE = 4;
}
