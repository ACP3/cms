<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Users\View\Block\Widget;

use ACP3\Core\ACL\ACLInterface;
use ACP3\Core\Modules\Modules;
use ACP3\Core\View\Block\AbstractBlock;
use ACP3\Core\View\Block\Context\BlockContext;

class UserMenuWidgetBlock extends AbstractBlock
{
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
    /**
     * @var ACLInterface
     */
    private $acl;
    /**
     * @var Modules
     */
    private $modules;

    /**
     * UserMenuWidgetBlock constructor.
     *
     * @param BlockContext $context
     * @param ACLInterface $acl
     * @param Modules      $modules
     */
    public function __construct(BlockContext $context, ACLInterface $acl, Modules $modules)
    {
        parent::__construct($context);

        $this->acl = $acl;
        $this->modules = $modules;
    }

    /**
     * {@inheritdoc}
     */
    public function render()
    {
        return [
            'modules' => $this->addModules(),
            'system' => $this->addSystemActions(),
        ];
    }

    /**
     * @return array
     */
    protected function addSystemActions(): array
    {
        $navSystem = [];
        foreach ($this->systemActions as $action) {
            $permissions = 'admin/system/' . $action['controller'] . '/' . $action['action'];
            if ($this->acl->hasPermission($permissions) === true) {
                $path = 'system/' . $action['controller'] . '/' . $action['action'];
                $navSystem[] = [
                    'path' => $path,
                    'name' => $this->translator->t('system', $action['phrase']),
                ];
            }
        }

        return $navSystem;
    }

    /**
     * @return array
     */
    protected function addModules(): array
    {
        $activeModules = $this->modules->getActiveModules();
        $navMods = [];
        foreach ($activeModules as $name => $info) {
            $dir = \strtolower($info['dir']);
            if (!\in_array($dir, ['acp', 'system']) && $this->acl->hasPermission('admin/' . $dir . '/index') === true) {
                $navMods[$name] = [
                    'path' => $dir,
                    'name' => $name,
                ];
            }
        }

        return $navMods;
    }
}
