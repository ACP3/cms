<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Acp\Event\Listener;

use ACP3\Core\Breadcrumb\Event\StepsBuildCacheEvent;
use ACP3\Core\I18n\Translator;

class AddAdminPanelLinkToBreadcrumbListener
{
    /**
     * @var \ACP3\Core\I18n\Translator
     */
    private $translator;

    public function __construct(Translator $translator)
    {
        $this->translator = $translator;
    }

    public function __invoke(StepsBuildCacheEvent $event)
    {
        $event->getSteps()->prepend($this->translator->t('acp', 'acp'), 'acp/acp');
    }
}
