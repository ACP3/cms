<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\System\ViewProviders;

use ACP3\Core\Modules;

class AdminModulesViewProvider
{
    /**
     * @var \ACP3\Core\Modules
     */
    private $modules;

    public function __construct(Modules $modules)
    {
        $this->modules = $modules;
    }

    public function __invoke(): array
    {
        $installedModules = $newModules = [];

        foreach ($this->modules->getAllModulesAlphabeticallySorted() as $key => $values) {
            if ($values['installable'] === false || $this->modules->isInstalled($values['name']) === true) {
                $installedModules[$key] = $values;
            } else {
                $newModules[$key] = $values;
            }
        }

        return [
            'installed_modules' => $installedModules,
            'new_modules' => $newModules,
        ];
    }
}
