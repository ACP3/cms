<?php
/**
 * Copyright (c) 2016 by the ACP3 Developers.
 * See the LICENCE file at the top-level module directory for licencing details.
 */

namespace ACP3\Installer\Modules\Errors\Controller\Index;

use ACP3\Installer\Core\Controller\AbstractInstallerAction;

/**
 * Class NotFound
 * @package ACP3\Installer\Modules\Errors\Controller\Index
 */
class NotFound extends AbstractInstallerAction
{
    public function execute()
    {
        $this->response->setStatusCode(404);
    }
}
