<?php

namespace ACP3\Modules\Errors\Controller;

use ACP3\Core;

/**
 * Class Index
 * @package ACP3\Modules\Errors\Controller
 */
class Index extends Core\Modules\Controller\Frontend
{
    public function action401()
    {
        header('HTTP/1.0 401 Unauthorized');
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