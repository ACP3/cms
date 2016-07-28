<?php
/**
 * Copyright (c) 2016 by the ACP3 Developers.
 * See the LICENCE file at the top-level module directory for licencing details.
 */

namespace ACP3\Modules\ACP3\Acp\Event\Listener;


use ACP3\Core\Breadcrumb\Event\StepsBuildCacheEvent;
use ACP3\Core\I18n\Translator;

/**
 * Class OnBuildAdminCacheEmptyStepsBeforeListener
 * @package ACP3\Modules\ACP3\Acp\Event\Listener
 */
class OnBuildAdminCacheEmptyStepsBeforeListener
{
    /**
     * @var \ACP3\Core\I18n\Translator
     */
    private $translator;

    /**
     * OnBreadcrumbStepsBuildCacheListener constructor.
     *
     * @param \ACP3\Core\I18n\Translator       $translator
     */
    public function __construct(Translator $translator)
    {
        $this->translator = $translator;
    }

    /**
     * @param \ACP3\Core\Breadcrumb\Event\StepsBuildCacheEvent $event
     */
    public function execute(StepsBuildCacheEvent $event)
    {
        $event->getSteps()->append($this->translator->t('acp', 'acp'), 'acp/acp');
    }
}
