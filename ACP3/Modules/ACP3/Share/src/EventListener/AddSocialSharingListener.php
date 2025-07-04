<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Share\EventListener;

use ACP3\Core\Controller\AreaEnum;
use ACP3\Core\Http\RequestInterface;
use ACP3\Core\Modules;
use ACP3\Core\View;
use ACP3\Core\View\Event\TemplateEvent;
use ACP3\Modules\ACP3\Share\Installer\Schema;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class AddSocialSharingListener implements EventSubscriberInterface
{
    public function __construct(private readonly Modules $modules, private readonly RequestInterface $request, private readonly View $view)
    {
    }

    public function __invoke(TemplateEvent $event): void
    {
        if ($this->modules->isInstalled(Schema::MODULE_NAME) === false) {
            return;
        }

        if ($this->request->getArea() === AreaEnum::AREA_FRONTEND) {
            $this->view->assign('sharing', [
                'path' => $this->request->getUriWithoutPages(),
            ]);
            $event->addContent($this->view->fetchTemplate('Share/Partials/add_social_sharing.tpl'));
        }
    }

    public static function getSubscribedEvents(): array
    {
        return [
            'share.layout.add_social_sharing' => '__invoke',
        ];
    }
}
