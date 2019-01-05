<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Filemanager;

use ACP3\Core;

class Helpers
{
    /**
     * @var \ACP3\Core\Environment\ApplicationPath
     */
    protected $appPath;

    /**
     * Helpers constructor.
     *
     * @param \ACP3\Core\Environment\ApplicationPath $appPath
     */
    public function __construct(Core\Environment\ApplicationPath $appPath)
    {
        $this->appPath = $appPath;
    }

    /**
     * @return string
     */
    public function getFilemanagerPath()
    {
        return $this->appPath->getWebRoot() . 'ACP3/Modules/ACP3/Filemanager/Resources/Assets/rich-filemanager/index.html';
    }
}
