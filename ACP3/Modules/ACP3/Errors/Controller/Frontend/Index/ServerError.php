<?php
/**
 * Copyright (c) 2016 by the ACP3 Developers.
 * See the LICENCE file at the top-level module directory for licencing details.
 */

namespace ACP3\Modules\ACP3\Errors\Controller\Frontend\Index;

use ACP3\Core;

/**
 * Class Index
 * @package ACP3\Modules\ACP3\Errors\Controller\Frontend\Index
 */
class ServerError extends Core\Controller\FrontendAction
{
    public function execute()
    {
        $this->response->setStatusCode(500);
    }
}
