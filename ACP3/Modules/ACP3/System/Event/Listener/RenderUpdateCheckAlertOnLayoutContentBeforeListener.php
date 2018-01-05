<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\System\Event\Listener;

use ACP3\Core\ACL;
use ACP3\Core\Controller\AreaEnum;
use ACP3\Core\Http\RequestInterface;
use ACP3\Core\View;
use ACP3\Modules\ACP3\System\Helper\UpdateCheck;

class RenderUpdateCheckAlertOnLayoutContentBeforeListener
{
    /**
     * @var UpdateCheck
     */
    private $updateCheck;
    /**
     * @var View
     */
    private $view;
    /**
     * @var RequestInterface
     */
    private $request;
    /**
     * @var ACL
     */
    private $acl;

    /**
     * RenderUpdateCheckAlertOnLayoutContentBeforeListener constructor.
     * @param ACL $acl
     * @param RequestInterface $request
     * @param View $view
     * @param UpdateCheck $updateCheck
     */
    public function __construct(
        ACL $acl,
        RequestInterface $request,
        View $view,
        UpdateCheck $updateCheck
    ) {
        $this->updateCheck = $updateCheck;
        $this->view = $view;
        $this->request = $request;
        $this->acl = $acl;
    }

    public function renderUpdateCheckAlert()
    {
        $update = $this->updateCheck->checkForNewVersion();

        if ($this->canRenderUpdateAlert($update['is_latest'])) {
            $this->view->assign('update', $update);
            $this->view->displayTemplate('System/Partials/alert_update_check.tpl');
        }
    }

    /**
     * @param bool $isLatestVersion
     * @return bool
     */
    private function canRenderUpdateAlert($isLatestVersion)
    {
        return $isLatestVersion === false
            && $this->request->getArea() === AreaEnum::AREA_ADMIN
            && $this->request->getFullPath() !== 'acp/system/maintenance/update_check/'
            && $this->acl->hasPermission('admin/system/maintenance/update_check');
    }
}
