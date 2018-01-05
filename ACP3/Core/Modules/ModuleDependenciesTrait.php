<?php
/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licencing details.
 */

namespace ACP3\Core\Modules;

use Composer\Json\JsonFile;

trait ModuleDependenciesTrait
{
    /**
     * @param string $path
     * @return array
     */
    protected function getModuleDependencies(string $path): array
    {
        $composer = (new JsonFile($path))->read();

        if (isset($composer['extra']['dependencies']) && \is_array($composer['extra']['dependencies'])) {
            return $composer['extra']['dependencies'];
        }

        return [];
    }
}
