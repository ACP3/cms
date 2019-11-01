<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Core\Assets;

class IncludeStylesheet extends AbstractIncludeAsset
{
    protected function getResourceDirectory(): string
    {
        return 'Assets/css';
    }

    protected function getFileExtension(): string
    {
        return 'css';
    }

    protected function getHtmlTag(): string
    {
        return '<link rel="stylesheet" type="text/css" href="%s">';
    }
}
