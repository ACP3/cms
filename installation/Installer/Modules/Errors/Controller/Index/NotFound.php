<?php
/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licencing details.
 */

namespace ACP3\Installer\Modules\Errors\Controller\Index;

use ACP3\Installer\Core\Controller\AbstractInstallerAction;
use Symfony\Component\HttpFoundation\Response;

class NotFound extends AbstractInstallerAction
{
    public function execute()
    {
        $this->response->setStatusCode(Response::HTTP_NOT_FOUND);
    }
}
