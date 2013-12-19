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
        \ACP3\Core\Auth $auth,
        \ACP3\Core\Breadcrumb $breadcrumb,
        \ACP3\Core\Date $date,
        \Doctrine\DBAL\Connection $db,
        \ACP3\Core\Lang $lang,
        \ACP3\Core\Session $session,
        \ACP3\Core\URI $uri,
        \ACP3\Core\View $view)
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