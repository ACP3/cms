<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Core\Modules;

use ACP3\Core\XML;

trait ModuleDependenciesTrait
{
    protected function getModuleDependencies(string $path): array
    {
        $dependencies = $this->getXml()->parseXmlFile($path, '/module/info/dependencies');

        if (isset($dependencies['module'])) {
            return \is_array($dependencies['module']) ? $dependencies['module'] : [$dependencies['module']];
        }

        return [];
    }

    /**
     * @return XML
     */
    abstract protected function getXml();
}
