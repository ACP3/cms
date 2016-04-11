<?php
/**
 * Copyright (c) 2016 by the ACP3 Developers.
 * See the LICENCE file at the top-level module directory for licencing details.
 */

namespace ACP3\Core\Breadcrumb\Event;


use ACP3\Core\Breadcrumb\Steps;
use Symfony\Component\EventDispatcher\Event;

/**
 * Class StepsBuildCacheEvent
 * @package ACP3\Core\Breadcrumb\Event
 */
class StepsBuildCacheEvent extends Event
{
    /**
     * @var \ACP3\Core\Breadcrumb\Steps
     */
    private $steps;

    /**
     * BreadcrumbStepsBuildCacheEvent constructor.
     *
     * @param \ACP3\Core\Breadcrumb\Steps $steps
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
