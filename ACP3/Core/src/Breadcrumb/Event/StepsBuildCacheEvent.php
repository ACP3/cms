<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Core\Breadcrumb\Event;

use ACP3\Core\Breadcrumb\Steps;
use Symfony\Contracts\EventDispatcher\Event;

class StepsBuildCacheEvent extends Event
{
    public function __construct(private Steps $steps)
    {
    }

    public function getSteps(): Steps
    {
        return $this->steps;
    }
}
