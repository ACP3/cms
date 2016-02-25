<?php
/**
 * Copyright (c) 2016 by the ACP3 Developers.
 * See the LICENCE file at the top-level module directory for licencing details.
 */

namespace ACP3\Modules\ACP3\Users\Controller\Frontend\Account;

use ACP3\Core\Controller\FrontendAction;
use ACP3\Core\Exceptions\UnauthorizedAccess;

/**
 * Class AbstractAction
 * @package ACP3\Modules\ACP3\Users\Controller\Frontend\Account
 */
abstract class AbstractAction extends FrontendAction
{
    public function preDispatch()
    {
        parent::preDispatch();

        if ($this->user->isAuthenticated() === false) {
            throw new UnauthorizedAccess();
        }
    }
}
