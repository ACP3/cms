<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Feeds\Extension;

interface FeedAvailabilityExtensionInterface
{
    /**
     * @return string
     */
    public function getModuleName();

    /**
     * @return array
     */
    public function fetchFeedItems();
}
