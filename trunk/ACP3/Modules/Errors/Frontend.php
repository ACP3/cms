<?php

namespace ACP3\Modules\Errors;

use ACP3\Core;

/**
 * Description of ErrorsFrontend
 *
 * @author Tino Goratsch
 */
class Frontend extends Core\Modules\Controller
{

    public function action403()
    {
        header('HTTP/1.0 403 Forbidden');
    }

    public function action404()
    {
        header('HTTP/1.0 404 not found');
    }

}