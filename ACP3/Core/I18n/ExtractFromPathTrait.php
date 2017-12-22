<?php
/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licencing details.
 */

namespace ACP3\Core\I18n;


trait ExtractFromPathTrait
{
    /**
     * @param string $filePath
     *
     * @return string
     */
    protected function getLanguagePackIsoCode($filePath)
    {
        return substr($filePath, strrpos($filePath, '/') + 1, -4);
    }

    /**
     * @param string $filePath
     *
     * @return string
     */
    protected function getModuleFromPath($filePath)
    {
        $pathArray = explode('/', $filePath);

        return $pathArray[count($pathArray) - 4];
    }
}
