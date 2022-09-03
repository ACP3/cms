<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Filemanager;

use ACP3\Core\Assets\FileResolver;
use ACP3\Modules\ACP3\Filemanager\Installer\Schema;

class Helpers
{
    public function __construct(private readonly FileResolver $fileResolver)
    {
    }

    public function getFilemanagerPath(): string
    {
        return $this->fileResolver->getWebStaticAssetPath(Schema::MODULE_NAME, 'Assets/rich-filemanager', 'index.html');
    }
}
