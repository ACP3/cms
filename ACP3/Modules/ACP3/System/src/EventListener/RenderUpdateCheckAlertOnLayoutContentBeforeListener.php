<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\System\EventListener;

use ACP3\Core\ACL;
use ACP3\Core\Controller\AreaEnum;
use ACP3\Core\Http\RequestInterface;
use ACP3\Core\View;
use ACP3\Core\View\Event\TemplateEvent;
use ACP3\Modules\ACP3\System\Helper\UpdateCheck;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class RenderUpdateCheckAlertOnLayoutContentBeforeListener implements EventSubscriberInterface
{
    public function __construct(private ACL $acl, private RequestInterface $request, private View $view, private UpdateCheck $updateCheck)
    {
    }

    public function __invoke(TemplateEvent $event): void
    {
        if (!$this->canRunUpdateCheck()) {
            return;
        }

        $update = $this->updateCheck->checkForNewVersion();

        if ($update && !$update['is_latest']) {
            $this->view->assign('update', $update);
            $event->addContent($this->view->fetchTemplate('System/Partials/alert_update_check.tpl'));
        }
    }

    private function canRunUpdateCheck(): bool
    {
        return $this->request->getArea() === AreaEnum::AREA_ADMIN
            && $this->request->getFullPath() !== 'acp/system/maintenance/update_check/'
            && $this->acl->hasPermission('admin/system/maintenance/update_check');
    }

    /**
     * {@inheritDoc}
     */
    public static function getSubscribedEvents(): array
    {
        return [
            'layout.content_before' => '__invoke',
        ];
    }
}
