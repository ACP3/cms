<?php
/**
 * Copyright (c) 2016 by the ACP3 Developers. See the LICENCE file at the top-level module directory for licencing details.
 */

namespace ACP3\Modules\ACP3\Errors\Controller\Frontend\Index;

use ACP3\Core;

/**
 * Class Index
 * @package ACP3\Modules\ACP3\Errors\Controller\Frontend\Index
 */
class Index extends Core\Modules\FrontendController
{
    public function action403()
    {
        $this->response->setStatusCode(403);
    }

    public function action404()
    {
        $this->response->setStatusCode(404);
    }

    public function action500()
    {
        $this->response->setStatusCode(500);
    }
}
