<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Seo\Helper\Enum;

use ACP3\Core\Enum\EnumValuesTrait;

enum IndexPaginatedContentEnum: string
{
    use EnumValuesTrait;

    case INDEX_ALL_PAGES = 'all';
    case INDEX_FIST_PAGE_ONLY = 'first';
}
