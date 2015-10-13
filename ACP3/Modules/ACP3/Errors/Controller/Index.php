<?php

namespace ACP3\Modules\ACP3\Errors\Controller;

use ACP3\Core;

/**
 * Class Index
 * @package ACP3\Modules\ACP3\Errors\Controller
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
