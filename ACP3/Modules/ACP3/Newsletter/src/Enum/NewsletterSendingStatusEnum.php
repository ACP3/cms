<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Newsletter\Enum;

use ACP3\Core\Enum\EnumValuesTrait;

enum NewsletterSendingStatusEnum: int
{
    use EnumValuesTrait;

    case NOT_SENT = 0;
    case SENT = 1;
}
