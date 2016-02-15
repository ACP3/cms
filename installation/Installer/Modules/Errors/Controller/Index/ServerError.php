<?php
/**
 * Copyright (c) 2016 by the ACP3 Developers. See the LICENCE file at the top-level module directory for licencing details.
 */

namespace ACP3\Installer\Modules\Errors\Controller\Index;

use ACP3\Installer\Core\Modules\AbstractInstallerController;

/**
 * Class ServerError
 * @package ACP3\Installer\Modules\Errors\Controller\Index
 */
class ServerError extends AbstractInstallerController
{
    public function execute()
    {
        header('HTTP/1.0 500 Internal Server Error');
    }
}
