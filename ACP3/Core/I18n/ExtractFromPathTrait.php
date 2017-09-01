<?php
/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENCE file at the top-level module directory for licencing details.
 */

namespace ACP3\Core\I18n;

trait ExtractFromPathTrait
{
    /**
     * @param string $filePath
     * @return string
     */
    protected function getLanguagePackIsoCode(string $filePath): string
    {
        return substr($filePath, strrpos($filePath, '/') + 1, -4);
    }

    /**
     * @param string $filePath
     * @return string
     */
    protected function getModuleFromPath(string $filePath): string
    {
        $pathArray = explode('/', $filePath);

        return $pathArray[count($pathArray) - 4];
    }
}
