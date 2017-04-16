<?php
/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENCE file at the top-level module directory for licencing details.
 */

namespace ACP3\Modules\ACP3\Acp\View\Block\Admin;


use ACP3\Core\ACL;
use ACP3\Core\Modules;
use ACP3\Core\View\Block\AbstractBlock;
use ACP3\Core\View\Block\Context\BlockContext;

class AllowedModulesBlock extends AbstractBlock
{
    /**
     * @var Modules
     */
    private $modules;
    /**
     * @var ACL
     */
    private $acl;

    /**
     * AllowedModulesBlock constructor.
     * @param BlockContext $context
     * @param ACL $acl
     * @param Modules $modules
     */
    public function __construct(BlockContext $context, ACL $acl, Modules $modules)
    {
        parent::__construct($context);

        $this->modules = $modules;
        $this->acl = $acl;
    }

    /**
     * @return mixed
     */
    public function render()
    {
        return [
            'modules' => $this->getAllowedModules()
        ];
    }

    /**
     * @return array
     */
    private function getAllowedModules()
    {
        $allowedModules = [];
        foreach ($this->modules->getActiveModules() as $name => $info) {
            $dir = strtolower($info['dir']);
            if ($this->acl->hasPermission('admin/' . $dir) === true && $dir !== 'acp') {
                $allowedModules[$name] = [
                    'name' => $name,
                    'dir' => $dir
                ];
            }
        }
        return $allowedModules;
    }
}
