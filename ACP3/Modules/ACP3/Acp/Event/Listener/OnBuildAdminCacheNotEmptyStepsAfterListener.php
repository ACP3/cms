<?php
/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licencing details.
 */

namespace ACP3\Modules\ACP3\Acp\Event\Listener;

use ACP3\Core\Breadcrumb\Event\StepsBuildCacheEvent;
use ACP3\Core\I18n\TranslatorInterface;

class OnBuildAdminCacheNotEmptyStepsAfterListener
{
    /**
     * @var \ACP3\Core\I18n\TranslatorInterface
     */
    private $translator;

    /**
     * OnBreadcrumbStepsBuildCacheListener constructor.
     *
     * @param \ACP3\Core\I18n\TranslatorInterface $translator
     */
    public function __construct(TranslatorInterface $translator)
    {
        $this->translator = $translator;
    }

    /**
     * @param \ACP3\Core\Breadcrumb\Event\StepsBuildCacheEvent $event
     */
    public function execute(StepsBuildCacheEvent $event)
    {
        $event->getSteps()->prepend($this->translator->t('acp', 'acp'), 'acp/acp');
    }
}
