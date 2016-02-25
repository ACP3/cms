<?php
/**
 * Copyright (c) 2016 by the ACP3 Developers.
 * See the LICENCE file at the top-level module directory for licencing details.
 */

namespace ACP3\Modules\ACP3\Users\Controller\Widget\Index;

use ACP3\Core;

/**
 * Class UserMenu
 * @package ACP3\Modules\ACP3\Users\Controller\Widget\Index
 */
class UserMenu extends Core\Controller\WidgetAction
{
    /**
     * @var array
     */
    protected $systemActions = [
        [
            'controller' => 'index',
            'action' => 'configuration',
            'phrase' => 'configuration'
        ],
        [
            'controller' => 'extensions',
            'action' => 'index',
            'phrase' => 'extensions'
        ],
        [
            'controller' => 'maintenance',
            'action' => 'index',
            'phrase' => 'maintenance'
        ],
    ];

    /**
     * Displays the user menu, if the user is logged in
     */
    public function execute()
    {
        if ($this->user->isAuthenticated() === true) {
            $userSidebar = [
                'current_page' => base64_encode($this->request->getOriginalQuery()),
                'modules' => $this->addModules(),
                'system' => $this->addSystemActions()
            ];

            $this->view->assign('user_sidebar', $userSidebar);

            $this->setTemplate('Users/Widget/index.user_menu.tpl');
        } else {
            $this->setNoOutput(true);
        }
    }

    /**
     * @return array
     */
    protected function addSystemActions()
    {
        $navSystem = [];
        foreach ($this->systemActions as $action) {
            $permissions = 'admin/system/' . $action['controller'] . '/' . $action['action'];
            if ($this->acl->hasPermission($permissions) === true) {
                $path = 'system/' . $action['controller'] . '/' . $action['action'] . '/';
                $navSystem[] = [
                    'path' => $path,
                    'name' => $this->translator->t('system', $action['phrase']),
                    'is_active' => strpos($this->request->getQuery(), $path) === 0
                ];
            }
        }

        return $navSystem;
    }

    /**
     * @return array
     */
    protected function addModules()
    {
        $activeModules = $this->modules->getActiveModules();
        $navMods = [];
        foreach ($activeModules as $name => $info) {
            $dir = strtolower($info['dir']);
            if (!in_array($dir, ['acp', 'system']) && $this->acl->hasPermission('admin/' . $dir . '/index') === true) {
                $navMods[$name] = [
                    'name' => $name,
                    'dir' => $dir,
                    'is_active' => $this->request->getArea() === Core\Controller\AreaEnum::AREA_ADMIN && $dir === $this->request->getModule()
                ];
            }
        }

        return $navMods;
    }
}
