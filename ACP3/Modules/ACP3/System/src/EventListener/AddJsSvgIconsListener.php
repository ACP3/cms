<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\System\EventListener;

use ACP3\Core\Helpers\View\Icon;
use ACP3\Core\View\Renderer\Smarty\Filters\Event\JsSvgIconEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class AddJsSvgIconsListener implements EventSubscriberInterface
{
    public function __construct(private readonly Icon $iconViewHelper)
    {
    }

    public function __invoke(JsSvgIconEvent $event): void
    {
        $event->addIcon('loadingIndicatorIcon', ($this->iconViewHelper)('solid', 'spinner', ['cssSelectors' => 'svg-icon--spin loading-indicator me-1']));
    }

    public static function getSubscribedEvents(): array
    {
        return [
            JsSvgIconEvent::class => '__invoke',
        ];
    }
}
