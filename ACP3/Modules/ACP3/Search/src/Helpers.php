<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Search;

use ACP3\Core;
use ACP3\Modules\ACP3\Search\Utility\SearchAvailabilityRegistrar;

class Helpers
{
    public function __construct(protected Core\ACL $acl, protected Core\Modules $modules, protected Core\Helpers\Forms $formsHelper, protected SearchAvailabilityRegistrar $availableModulesRegistrar)
    {
    }

    /**
     * Gibt die für die Suche verfügbaren Module zurück.
     *
     * @return array<string, array<string, mixed>>
     */
    public function getModules(): array
    {
        $searchModules = [];
        foreach ($this->availableModulesRegistrar->getAvailableModuleNames() as $module) {
            if ($this->acl->hasPermission('frontend/' . $module) === true) {
                $info = $this->modules->getModuleInfo($module);
                $name = $info['name'];

                $searchModules[$name] = [
                    'dir' => $module,
                    'checked' => $this->formsHelper->selectEntry('mods', $module, $module, 'checked'),
                    'name' => $name,
                ];
            }
        }
        ksort($searchModules);

        return $searchModules;
    }
}
