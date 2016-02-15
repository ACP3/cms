<?php
/**
 * Copyright (c) 2016 by the ACP3 Developers. See the LICENCE file at the top-level module directory for licencing details.
 */

namespace ACP3\Modules\ACP3\Users\Controller\Sidebar\Index;

use ACP3\Core;

/**
 * Class UserMenu
 * @package ACP3\Modules\ACP3\Users\Controller\Sidebar\Index
 */
class UserMenu extends Core\Modules\Controller
{
    /**
     * Displays the user menu, if the user is logged in
     */
    public function execute()
    {
        if ($this->user->isAuthenticated() === true) {
            $userSidebar = [];
            $userSidebar['page'] = base64_encode(($this->request->getArea() === 'admin' ? 'acp/' : '') . $this->request->getQuery());

            $activeModules = $this->modules->getActiveModules();
            $navMods = $navSystem = [];
            $hasAccessToSystem = false;

            foreach ($activeModules as $name => $info) {
                $dir = strtolower($info['dir']);
                if ($dir !== 'acp' && $this->acl->hasPermission('admin/' . $dir . '/index') === true) {
                    if ($dir === 'system') {
                        $hasAccessToSystem = true;
                    } else {
                        $navMods[$name]['name'] = $name;
                        $navMods[$name]['dir'] = $dir;
                        $navMods[$name]['active'] = $this->request->getArea() === 'admin' && $dir === $this->request->getModule() ? ' class="active"' : '';
                    }
                }
            }

            if (!empty($navMods)) {
                $userSidebar['modules'] = $navMods;
            }

            // If the user has access to the system module, display some more options
            if ($hasAccessToSystem === true) {
                $i = 0;
                if ($this->acl->hasPermission('admin/system/index/configuration') === true) {
                    $navSystem[$i]['path'] = 'system/index/configuration/';
                    $navSystem[$i]['name'] = $this->translator->t('system', 'configuration');
                    $navSystem[$i]['active'] = $this->request->getQuery() === $navSystem[$i]['path'] ? ' class="active"' : '';
                }
                if ($this->acl->hasPermission('admin/system/extensions/index') === true) {
                    $i++;
                    $navSystem[$i]['path'] = 'system/extensions/';
                    $navSystem[$i]['name'] = $this->translator->t('system', 'extensions');
                    $navSystem[$i]['active'] = strpos($this->request->getQuery(), $navSystem[$i]['path']) === 0 ? ' class="active"' : '';
                }
                if ($this->acl->hasPermission('admin/system/maintenance/index') === true) {
                    $i++;
                    $navSystem[$i]['path'] = 'system/maintenance/';
                    $navSystem[$i]['name'] = $this->translator->t('system', 'maintenance');
                    $navSystem[$i]['active'] = strpos($this->request->getQuery(), $navSystem[$i]['path']) === 0 ? ' class="active"' : '';
                }
                $userSidebar['system'] = $navSystem;
            }

            $this->view->assign('user_sidebar', $userSidebar);

            $this->setTemplate('Users/Sidebar/index.user_menu.tpl');
        } else {
            $this->setNoOutput(true);
        }
    }
}
