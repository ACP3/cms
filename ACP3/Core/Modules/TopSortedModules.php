<?php
/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENCE file at the top-level module directory for licencing details.
 */

namespace ACP3\Core\Modules;


use MJS\TopSort\Implementations\StringSort;

class TopSortedModules extends AllModules
{
    /**
     * Returns a collection of ACP3 modules which is sorted topologically
     *
     * @return array
     */
    public function getAll(): array
    {
        $topSort = new StringSort();

        $modules = parent::getAll();
        foreach ($modules as $module) {
            $topSort->add(strtolower($module['dir']), $module['dependencies']);
        }

        $topSortedModules = [];
        foreach ($topSort->sort() as $module) {
            $topSortedModules[$module] = $modules[$module];
        }

        return $topSortedModules;
    }
}
