<?php

namespace ACP3\Installer\Modules\Errors;

/**
 * Module controller of the Errors installer module
 *
 * @author Tino Goratsch
 */
class Errors extends \ACP3\Installer\Core\Modules\Controller
{

    public function action404()
    {
        header('HTTP/1.0 404 not found');
    }

}
