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
    /**
     * @var Core\ACL\ACLInterface
     */
    protected $acl;
    /**
     * @var \ACP3\Core\Modules\Modules
     */
    protected $modules;
    /**
     * @var \ACP3\Core\Helpers\Forms
     */
    protected $formsHelper;
    /**
     * @var SearchAvailabilityRegistrar
     */
    protected $availableModulesRegistrar;

    /**
     * Helpers constructor.
     * @param Core\ACL\ACLInterface $acl
     * @param \ACP3\Core\Modules\Modules $modules
     * @param Core\Helpers\Forms $formsHelper
     * @param SearchAvailabilityRegistrar $availableModulesRegistrar
     */
    public function __construct(
        Core\ACL\ACLInterface $acl,
        Core\Modules\Modules $modules,
        Core\Helpers\Forms $formsHelper,
        SearchAvailabilityRegistrar $availableModulesRegistrar
    ) {
        $this->acl = $acl;
        $this->modules = $modules;
        $this->formsHelper = $formsHelper;
        $this->availableModulesRegistrar = $availableModulesRegistrar;
    }

    /**
     * Gibt die für die Suche verfügbaren Module zurück
     *
     * @return array
     */
    public function getModules()
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
        \ksort($searchModules);

        return $searchModules;
    }
}
