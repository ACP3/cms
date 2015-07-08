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
        header('HTTP/1.0 403 Access Forbidden');
    }

    public function action404()
    {
        header('HTTP/1.0 404 Not Found');
    }

    public function action500()
    {
        header('HTTP/1.0 500 Internal Server Error');
    }
}
