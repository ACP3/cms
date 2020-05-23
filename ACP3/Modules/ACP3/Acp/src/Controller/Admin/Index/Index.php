<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Acp\Controller\Admin\Index;

use ACP3\Core;
use ACP3\Core\Controller\Context;

class Index extends Core\Controller\AbstractFrontendAction
{
    /**
     * @var \ACP3\Core\ACL
     */
    private $acl;
    /**
     * @var \ACP3\Core\Modules
     */
    private $modules;

    public function __construct(Context\FrontendContext $context, Core\ACL $acl, Core\Modules $modules)
    {
        parent::__construct($context);

        $this->acl = $acl;
        $this->modules = $modules;
    }

    public function execute(): array
    {
        return [
            'modules' => $this->getAllowedModules(),
        ];
    }

    protected function getAllowedModules(): array
    {
        $allowedModules = [];

        foreach ($this->modules->getActiveModules() as $info) {
            $moduleName = \strtolower($info['name']);
            if ($moduleName !== 'acp' && $this->acl->hasPermission('admin/' . $moduleName) === true) {
                $allowedModules[$moduleName] = [
                    'name' => $moduleName,
                ];
            }
        }

        return $allowedModules;
    }
}
