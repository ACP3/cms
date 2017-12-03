<?php
/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENCE file at the top-level module directory for licencing details.
 */

namespace ACP3\Core\Modules;


class AlphabeticallySortedModules extends AllModules
{
    /**
     * Returns a collection of ACP3 modules which is sorted alphabetically
     *
     * @return array
     */
    public function getAll(): array
    {
        $allModulesAlphabeticallySorted = [];
        foreach (parent::getAll() as $info) {
            $allModulesAlphabeticallySorted[$info['name']] = $info;
        }

        ksort($allModulesAlphabeticallySorted);

        return $allModulesAlphabeticallySorted;
    }

}
