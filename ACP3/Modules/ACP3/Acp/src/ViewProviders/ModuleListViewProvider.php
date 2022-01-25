<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Acp\ViewProviders;

use ACP3\Core\ACL;
use ACP3\Core\I18n\Translator;
use ACP3\Core\Modules;

class ModuleListViewProvider
{
    public function __construct(private ACL $acl, private Modules $modules, private Translator $translator)
    {
    }

    /**
     * @return array<string, array<string, string>>
     */
    public function __invoke(): array
    {
        return [
            'modules' => $this->getAllowedModules(),
        ];
    }

    /**
     * @return array<string, string>
     */
    private function getAllowedModules(): array
    {
        $allowedModules = [];

        foreach ($this->modules->getInstalledModules() as $info) {
            $moduleName = strtolower($info['name']);
            if ($moduleName !== 'acp' && $this->acl->hasPermission('admin/' . $moduleName) === true) {
                $allowedModules[$this->translator->t($moduleName, $moduleName)] = $moduleName;
            }
        }

        ksort($allowedModules);

        return $allowedModules;
    }
}
