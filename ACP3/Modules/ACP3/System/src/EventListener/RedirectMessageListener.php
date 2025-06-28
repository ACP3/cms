<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\System\EventListener;

use ACP3\Core\Helpers\RedirectMessages;
use ACP3\Core\View;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class RedirectMessageListener implements EventSubscriberInterface
{
    public function __construct(
        private readonly RedirectMessages $redirectMessages,
        private readonly View $view,
    ) {
    }

    public static function getSubscribedEvents(): array
    {
        return [
            'layout.content_before' => 'onLayoutContentBefore',
        ];
    }

    public function onLayoutContentBefore(View\Event\TemplateEvent $event): void
    {
        $this->view->assign('redirect', $this->redirectMessages->getMessage());

        $event->addContent($this->view->fetchTemplate('System/Partials/redirect_message.tpl'));
    }
}
