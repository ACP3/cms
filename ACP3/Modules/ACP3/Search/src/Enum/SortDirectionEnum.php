<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Search\Enum;

use ACP3\Core\Enum\EnumValuesTrait;

enum SortDirectionEnum: string
{
    use EnumValuesTrait;

    case ASC = 'asc';
    case DESC = 'desc';
}
