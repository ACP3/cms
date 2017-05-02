<?php
/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENCE file at the top-level module directory for licencing details.
 */

namespace ACP3\Modules\ACP3\Users\View\Block\Widget;


use ACP3\Core\ACL;
use ACP3\Core\Controller\AreaEnum;
use ACP3\Core\Modules;
use ACP3\Core\View;
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
            'phrase' => 'settings'
        ],
        [
            'controller' => 'extensions',
            'action' => '',
            'phrase' => 'extensions'
        ],
        [
            'controller' => 'maintenance',
            'action' => '',
            'phrase' => 'maintenance'
        ],
    ];
    /**
     * @var ACL
     */
    private $acl;
    /**
     * @var Modules
     */
    private $modules;

    /**
     * UserMenuWidgetBlock constructor.
     * @param BlockContext $context
     * @param ACL $acl
     * @param Modules $modules
     */
    public function __construct(BlockContext $context, ACL $acl, Modules $modules)
    {
        parent::__construct($context);

        $this->acl = $acl;
        $this->modules = $modules;
    }

    /**
     * @inheritdoc
     */
    public function render()
    {
        return [
            'modules' => $this->addModules(),
            'system' => $this->addSystemActions()
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
            $dir = strtolower($info['dir']);
            if (!in_array($dir, ['acp', 'system']) && $this->acl->hasPermission('admin/' . $dir . '/index') === true) {
                $navMods[$name] = [
                    'path' => $dir,
                    'name' => $name,
                ];
            }
        }

        return $navMods;
    }
}
