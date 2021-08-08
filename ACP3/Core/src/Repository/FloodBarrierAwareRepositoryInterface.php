<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Core\Repository;

interface FloodBarrierAwareRepositoryInterface
{
    public function getLastDateFromIp(string $ipAddress): ?string;
}
