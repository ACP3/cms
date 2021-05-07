<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Core\I18n;

/**
 * @deprecated since version v5.15.0. To be removed with version 6.0.0.
 */
trait ExtractFromPathTrait
{
    protected function getLanguagePackIsoCode(string $filePath): string
    {
        return pathinfo($filePath)['filename'];
    }

    protected function getModuleFromPath(string $filePath): string
    {
        $pathArray = explode('/', $filePath);

        return $pathArray[\count($pathArray) - 4];
    }
}
