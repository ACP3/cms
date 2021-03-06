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
    /**
     * @var \ACP3\Core\Breadcrumb\Steps
     */
    private $steps;

    /**
     * BreadcrumbStepsBuildCacheEvent constructor.
     */
    public function __construct(Steps $steps)
    {
        $this->steps = $steps;
    }

    /**
     * @return \ACP3\Core\Breadcrumb\Steps
     */
    public function getSteps()
    {
        return $this->steps;
    }
}
