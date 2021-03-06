<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Core\Assets;

class IncludeJs extends AbstractIncludeAsset
{
    protected function getResourceDirectory(): string
    {
        return 'Assets/js';
    }

    protected function getFileExtension(): string
    {
        return 'js';
    }

    protected function getHtmlTag(): string
    {
        return '<script defer src="%s"></script>';
    }
}
