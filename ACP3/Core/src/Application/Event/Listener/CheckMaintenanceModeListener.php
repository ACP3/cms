<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Core\Application\Event\Listener;

use ACP3\Core\Application\Event\ControllerActionBeforeDispatchEvent;
use ACP3\Core\Application\Exception\MaintenanceModeActiveException;
use ACP3\Core\Controller\AreaEnum;
use ACP3\Core\Environment\ApplicationPath;
use ACP3\Core\Http\RequestInterface;
use ACP3\Core\Settings\SettingsInterface;
use ACP3\Core\View;
use Symfony\Component\HttpFoundation\Response;

class CheckMaintenanceModeListener
{
    /**
     * @var \ACP3\Core\Environment\ApplicationPath
     */
    private $appPath;
    /**
     * @var \ACP3\Core\Http\RequestInterface
     */
    private $request;
    /**
     * @var \ACP3\Core\Settings\SettingsInterface
     */
    private $settings;
    /**
     * @var \ACP3\Core\View
     */
    private $view;

    public function __construct(
        ApplicationPath $appPath,
        RequestInterface $request,
        SettingsInterface $settings,
        View $view)
    {
        $this->appPath = $appPath;
        $this->request = $request;
        $this->settings = $settings;
        $this->view = $view;
    }

    public function __invoke(ControllerActionBeforeDispatchEvent $event)
    {
        if ($this->canShowMaintenanceMessage()) {
            $this->renderMaintenanceMessage();
        }
    }

    /**
     * Checks, whether the maintenance mode is active.
     */
    private function canShowMaintenanceMessage(): bool
    {
        return (bool) $this->settings->getSettings('system')['maintenance_mode'] === true &&
            \in_array($this->request->getArea(), [AreaEnum::AREA_ADMIN, AreaEnum::AREA_WIDGET], true) === false &&
            \strpos($this->request->getQuery(), 'users/index/login/') !== 0;
    }

    private function renderMaintenanceMessage(): void
    {
        $this->view->assign([
            'PAGE_TITLE' => 'ACP3',
            'ROOT_DIR' => $this->appPath->getWebRoot(),
            'CONTENT' => $this->settings->getSettings('system')['maintenance_message'],
        ]);

        throw new MaintenanceModeActiveException($this->view->fetchTemplate('System/layout.maintenance.tpl'), Response::HTTP_SERVICE_UNAVAILABLE);
    }
}
