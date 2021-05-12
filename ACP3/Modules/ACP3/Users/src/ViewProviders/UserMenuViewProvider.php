<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Users\ViewProviders;

use ACP3\Core\ACL;
use ACP3\Core\Controller\AreaEnum;
use ACP3\Core\Http\RequestInterface;
use ACP3\Core\I18n\Translator;
use ACP3\Core\Modules;

class UserMenuViewProvider
{
    /**
     * @var array
     */
    private static $systemActions = [
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

    /**
     * @var \ACP3\Core\Http\RequestInterface
     */
    private $request;
    /**
     * @var \ACP3\Core\ACL
     */
    private $acl;
    /**
     * @var \ACP3\Core\Modules
     */
    private $modules;
    /**
     * @var \ACP3\Core\I18n\Translator
     */
    private $translator;

    public function __construct(RequestInterface $request, ACL $acl, Modules $modules, Translator $translator)
    {
        $this->request = $request;
        $this->acl = $acl;
        $this->modules = $modules;
        $this->translator = $translator;
    }

    public function __invoke(): array
    {
        $prefix = $this->request->getArea() === AreaEnum::AREA_ADMIN ? 'acp/' : '';

        return [
            'user_sidebar' => [
                'current_page' => base64_encode($prefix . $this->request->getQuery()),
                'modules' => $this->getModules(),
                'system' => $this->getSystemActions(),
            ],
        ];
    }

    private function getSystemActions(): array
    {
        $navSystem = [];
        foreach (static::$systemActions as $action) {
            $permissions = 'admin/system/' . $action['controller'] . '/' . $action['action'];
            if ($this->acl->hasPermission($permissions) === true) {
                $path = 'system/' . $action['controller'] . '/' . $action['action'];
                $navSystem[] = [
                    'path' => $path,
                    'name' => $this->translator->t('system', $action['phrase']),
                    'is_active' => strpos($this->request->getQuery(), $path) === 0,
                ];
            }
        }

        return $navSystem;
    }

    private function getModules(): array
    {
        $activeModules = $this->modules->getInstalledModules();
        $navMods = [];
        foreach ($activeModules as $name => $info) {
            if (!\in_array($info['name'], ['acp', 'system'])
                && $this->acl->hasPermission('admin/' . $info['name'] . '/index') === true
            ) {
                $navMods[$this->translator->t($name, $name)] = [
                    'name' => $name,
                    'is_active' => $this->request->getArea() === AreaEnum::AREA_ADMIN && $info['name'] === $this->request->getModule(),
                ];
            }
        }

        ksort($navMods);

        return $navMods;
    }
}
