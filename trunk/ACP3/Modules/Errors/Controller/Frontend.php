<?php

namespace ACP3\Modules\Errors\Controller;

use ACP3\Core;

/**
 * Description of ErrorsFrontend
 *
 * @author Tino Goratsch
 */
class Frontend extends Core\Modules\Controller
{
    public function __construct(
        Core\Auth $auth,
        Core\Breadcrumb $breadcrumb,
        Core\Date $date,
        \Doctrine\DBAL\Connection $db,
        Core\Lang $lang,
        Core\Session $session,
        Core\URI $uri,
        Core\View $view)
    {
        parent::__construct($auth, $breadcrumb, $date, $db, $lang, $session, $uri, $view);
    }

    public function action403()
    {
        header('HTTP/1.0 403 Forbidden');
    }

    public function action404()
    {
        header('HTTP/1.0 404 not found');
    }

}