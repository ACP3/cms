<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Acp\EventListener;

use ACP3\Core\Breadcrumb\Event\StepsBuildCacheEvent;
use ACP3\Core\I18n\Translator;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class AddAdminPanelLinkToBreadcrumbListener implements EventSubscriberInterface
{
    public function __construct(private readonly Translator $translator)
    {
    }

    public function __invoke(StepsBuildCacheEvent $event): void
    {
        $event->getSteps()->prepend($this->translator->t('acp', 'acp'), 'acp/acp');
    }

    /**
     * {@inheritDoc}
     */
    public static function getSubscribedEvents(): array
    {
        return [
            'core.breadcrumb.steps.build_admin_cache_not_empty_steps_after' => '__invoke',
        ];
    }
}
