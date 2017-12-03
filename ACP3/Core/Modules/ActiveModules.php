<?php
/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENCE file at the top-level module directory for licencing details.
 */

namespace ACP3\Core\Modules;


class ActiveModules extends AlphabeticallySortedModules
{
    /**
     * Returns a collection of all currently installed and active ACP3 modules
     *
     * @return array
     */
    public function getActiveModules(): array
    {
        $modules = parent::getAll();

        foreach ($modules as $key => $values) {
            if ($values['active'] === false) {
                unset($modules[$key]);
            }
        }

        return $modules;
    }
}
