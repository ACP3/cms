<?php
/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENCE file at the top-level module directory for licencing details.
 */

namespace ACP3\Core\Modules;


class InstalledModules extends AlphabeticallySortedModules
{
    /**
     * Returns a collection of all currently installed ACP3 modules
     *
     * @return array
     */
    public function getAll(): array
    {
        $modules = parent::getAll();

        foreach ($modules as $key => $values) {
            if ($values['installed'] === false) {
                unset($modules[$key]);
            }
        }

        return $modules;
    }
}
