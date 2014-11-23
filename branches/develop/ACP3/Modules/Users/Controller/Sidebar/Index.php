<?php

namespace ACP3\Modules\Users\Controller\Sidebar;

use ACP3\Core;
use ACP3\Modules\Users;

/**
 * Class Index
 * @package ACP3\Modules\Users\Controller\Sidebar
 */
class Index extends Core\Modules\Controller
{
    /**
     * @var Core\Config
     */
    protected $usersConfig;

    /**
     * @param Core\Context $context
     * @param Core\Config $usersConfig
     */
    public function __construct(
        Core\Context $context,
        Core\Config $usersConfig)
    {
        parent::__construct($context);

        $this->usersConfig = $usersConfig;
    }

    /**
     * Displays the login mask, if the user is not already logged in
     */
    public function actionLogin()
    {
        if ($this->auth->isUser() === false) {
            $currentPage = base64_encode(($this->request->area === 'admin' ? 'acp/' : '') . $this->request->query);

            $settings = $this->usersConfig->getSettings();

            $this->view->assign('enable_registration', $settings['enable_registration']);
            $this->view->assign('redirect_uri', isset($_POST['redirect_uri']) ? $_POST['redirect_uri'] : $currentPage);

            $this->setTemplate('Users/Sidebar/index.login.tpl');
        } else {
            $this->setNoOutput(true);
        }
    }

    /**
     * Displays the user menu, if the user is logged in
     */
    public function actionUserMenu()
    {
        if ($this->auth->isUser() === true) {
            $userSidebar = [];
            $userSidebar['page'] = base64_encode(($this->request->area === 'admin' ? 'acp/' : '') . $this->request->query);

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
                        $navMods[$name]['active'] = $this->request->area === 'admin' && $dir === $this->request->mod ? ' class="active"' : '';
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
                    $navSystem[$i]['name'] = $this->lang->t('system', 'configuration');
                    $navSystem[$i]['active'] = $this->request->query === $navSystem[$i]['path'] ? ' class="active"' : '';
                }
                if ($this->acl->hasPermission('admin/system/extensions/index') === true) {
                    $i++;
                    $navSystem[$i]['path'] = 'system/extensions/';
                    $navSystem[$i]['name'] = $this->lang->t('system', 'extensions');
                    $navSystem[$i]['active'] = strpos($this->request->query, $navSystem[$i]['path']) === 0 ? ' class="active"' : '';
                }
                if ($this->acl->hasPermission('admin/system/maintenance/index') === true) {
                    $i++;
                    $navSystem[$i]['path'] = 'system/maintenance/';
                    $navSystem[$i]['name'] = $this->lang->t('system', 'maintenance');
                    $navSystem[$i]['active'] = strpos($this->request->query, $navSystem[$i]['path']) === 0 ? ' class="active"' : '';
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