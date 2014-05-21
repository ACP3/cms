<?php

namespace ACP3\Modules\Users\Controller\Sidebar;

use ACP3\Core;
use ACP3\Modules\Users;

/**
 * Description of UsersFrontend
 *
 * @author Tino Goratsch
 */
class Index extends Core\Modules\Controller\Sidebar
{
    public function actionLogin()
    {
        // Usermenü anzeigen, falls der Benutzer eingeloggt ist
        if ($this->auth->isUser() === false) {
            $currentPage = base64_encode(($this->uri->area === 'admin' ? 'acp/' : '') . $this->uri->query);
            $settings = Core\Config::getSettings('users');

            $this->view->assign('enable_registration', $settings['enable_registration']);
            $this->view->assign('redirect_uri', isset($_POST['redirect_uri']) ? $_POST['redirect_uri'] : $currentPage);

            $this->setLayout('Users/Sidebar/index.login.tpl');
        }
    }

    public function actionUserMenu()
    {
        if ($this->auth->isUser() === true) {
            $userSidebar = array();
            $userSidebar['page'] = base64_encode(($this->uri->area === 'admin' ? 'acp/' : '') . $this->uri->query);

            // Module holen
            $activeModules = Core\Modules::getActiveModules();
            $navMods = $navSystem = array();
            $hasAccessToSystem = false;

            foreach ($activeModules as $name => $info) {
                $dir = strtolower($info['dir']);
                if ($dir !== 'acp' && Core\Modules::hasPermission('admin/' . $dir . '/index') === true) {
                    if ($dir === 'system') {
                        $hasAccessToSystem = true;
                    } else {
                        $navMods[$name]['name'] = $name;
                        $navMods[$name]['dir'] = $dir;
                        $navMods[$name]['active'] = $this->uri->area === 'admin' && $dir === $this->uri->mod ? ' class="active"' : '';
                    }
                }
            }
            if (!empty($navMods)) {
                $userSidebar['modules'] = $navMods;
            }

            if ($hasAccessToSystem === true) {
                $i = 0;
                if (Core\Modules::hasPermission('admin/system/index/configuration') === true) {
                    $navSystem[$i]['path'] = 'system/index/configuration/';
                    $navSystem[$i]['name'] = $this->lang->t('system', 'configuration');
                    $navSystem[$i]['active'] = $this->uri->query === $navSystem[$i]['path'] ? ' class="active"' : '';
                }
                if (Core\Modules::hasPermission('admin/system/extensions/index') === true) {
                    $i++;
                    $navSystem[$i]['path'] = 'system/extensions/';
                    $navSystem[$i]['name'] = $this->lang->t('system', 'extensions');
                    $navSystem[$i]['active'] = strpos($this->uri->query, $navSystem[$i]['path']) === 0 ? ' class="active"' : '';
                }
                if (Core\Modules::hasPermission('admin/system/maintenance/index') === true) {
                    $i++;
                    $navSystem[$i]['path'] = 'system/maintenance/';
                    $navSystem[$i]['name'] = $this->lang->t('system', 'maintenance');
                    $navSystem[$i]['active'] = strpos($this->uri->query, $navSystem[$i]['path']) === 0 ? ' class="active"' : '';
                }
                $userSidebar['system'] = $navSystem;
            }

            $this->view->assign('user_sidebar', $userSidebar);

            $this->setLayout('Users/Sidebar/index.user_menu.tpl');
        }
    }

}