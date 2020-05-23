<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Users\Controller\Widget\Index;

use ACP3\Core;
use ACP3\Modules\ACP3\System\Installer\Schema;

class UserMenu extends Core\Controller\AbstractWidgetAction
{
    use Core\Cache\CacheResponseTrait;

    /**
     * @var \ACP3\Core\ACL
     */
    private $acl;
    /**
     * @var \ACP3\Core\Modules
     */
    private $modules;

    /**
     * @var array
     */
    protected $systemActions = [
        [
            'controller' => 'index',
            'action' => 'settings',
            'phrase' => 'settings',
        ],
        [
            'controller' => 'extensions',
            'action' => '',
            'phrase' => 'extensions',
        ],
        [
            'controller' => 'maintenance',
            'action' => '',
            'phrase' => 'maintenance',
        ],
    ];

    public function __construct(
        Core\Controller\Context\WidgetContext $context,
        Core\ACL $acl,
        Core\Modules $modules)
    {
        parent::__construct($context);

        $this->acl = $acl;
        $this->modules = $modules;
    }

    /**
     * Displays the user menu, if the user is logged in.
     */
    public function execute(): array
    {
        $this->setCacheResponseCacheable($this->config->getSettings(Schema::MODULE_NAME)['cache_lifetime']);

        if ($this->user->isAuthenticated() === true) {
            $prefix = $this->request->getArea() === Core\Controller\AreaEnum::AREA_ADMIN ? 'acp/' : '';

            $userSidebar = [
                'current_page' => \base64_encode($prefix . $this->request->getQuery()),
                'modules' => $this->getModules(),
                'system' => $this->getSystemActions(),
            ];

            return [
                'user_sidebar' => $userSidebar,
            ];
        }

        $this->setContent(false);

        return [];
    }

    protected function getSystemActions(): array
    {
        $navSystem = [];
        foreach ($this->systemActions as $action) {
            $permissions = 'admin/system/' . $action['controller'] . '/' . $action['action'];
            if ($this->acl->hasPermission($permissions) === true) {
                $path = 'system/' . $action['controller'] . '/' . $action['action'];
                $navSystem[] = [
                    'path' => $path,
                    'name' => $this->translator->t('system', $action['phrase']),
                    'is_active' => \strpos($this->request->getQuery(), $path) === 0,
                ];
            }
        }

        return $navSystem;
    }

    protected function getModules(): array
    {
        $activeModules = $this->modules->getActiveModules();
        $navMods = [];
        foreach ($activeModules as $name => $info) {
            if (!\in_array($info['name'], ['acp', 'system'])
                && $this->acl->hasPermission('admin/' . $info['name'] . '/index') === true
            ) {
                $navMods[$name] = [
                    'name' => $name,
                    'is_active' => $this->request->getArea() === Core\Controller\AreaEnum::AREA_ADMIN && $info['name'] === $this->request->getModule(),
                ];
            }
        }

        return $navMods;
    }
}
