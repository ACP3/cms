<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\System\Helper;

use ACP3\Core\Environment\ApplicationMode;

class CanUsePageCache
{
    public function __construct(private readonly ApplicationMode $environment)
    {
    }

    public function canUsePageCache(): bool
    {
        return $this->environment === ApplicationMode::PRODUCTION;
    }
}
