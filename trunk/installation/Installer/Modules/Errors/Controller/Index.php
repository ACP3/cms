<?php

namespace ACP3\Installer\Modules\Errors\Controller;

/**
 * Class Index
 * @package ACP3\Installer\Modules\Errors\Controller
 */
class Index extends \ACP3\Installer\Core\Modules\Controller
{

    public function action404()
    {
        header('HTTP/1.0 404 not found');
    }

}
