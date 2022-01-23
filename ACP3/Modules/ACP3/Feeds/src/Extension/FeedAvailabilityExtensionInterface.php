<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Feeds\Extension;

interface FeedAvailabilityExtensionInterface
{
    public function getModuleName(): string;

    /**
     * @return array<array<string, mixed>>
     */
    public function fetchFeedItems(): array;
}
