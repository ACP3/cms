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
    /**
     * @var Icon
     */
    private $iconViewHelper;

    public function __construct(Icon $iconViewHelper)
    {
        $this->iconViewHelper = $iconViewHelper;
    }

    public function __invoke(JsSvgIconEvent $event): void
    {
        $event->addIcon('loadingLayerIcon', ($this->iconViewHelper)('solid', 'spinner', ['pathOnly' => true]));
        $event->addIcon('validationFailedIcon', ($this->iconViewHelper)('solid', 'exclamation-circle', ['pathOnly' => true]));
    }

    public static function getSubscribedEvents(): array
    {
        return [
            JsSvgIconEvent::class => '__invoke',
        ];
    }
}
