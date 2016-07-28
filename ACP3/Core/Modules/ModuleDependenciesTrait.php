<?php
/**
 * Copyright (c) 2016 by the ACP3 Developers.
 * See the LICENCE file at the top-level module directory for licencing details.
 */

namespace ACP3\Core\Modules;


use ACP3\Core\XML;

trait ModuleDependenciesTrait
{
    /**
     * @param string $path
     * @return array
     */
    protected function getModuleDependencies($path)
    {
        $dependencies = $this->getXml()->parseXmlFile($path, '/module/info/dependencies');

        if (isset($dependencies['module'])) {
            return is_array($dependencies['module']) ? $dependencies['module'] : [$dependencies['module']];
        }

        return [];
    }

    /**
     * @return XML
     */
    abstract protected function getXml();
}
