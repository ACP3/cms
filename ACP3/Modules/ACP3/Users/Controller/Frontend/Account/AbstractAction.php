<?php
/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licencing details.
 */

namespace ACP3\Modules\ACP3\Users\Controller\Frontend\Account;

use ACP3\Core\Authentication\Exception\UnauthorizedAccessException;
use ACP3\Core\Controller\AbstractFrontendAction;

/**
 * Class AbstractAction
 * @package ACP3\Modules\ACP3\Users\Controller\Frontend\Account
 */
abstract class AbstractAction extends AbstractFrontendAction
{
    /**
     * @return $this|void
     * @throws UnauthorizedAccessException
     * @throws \ACP3\Core\ACL\Exception\AccessForbiddenException
     */
    public function preDispatch()
    {
        parent::preDispatch();

        if ($this->user->isAuthenticated() === false) {
            throw new UnauthorizedAccessException();
        }
    }
}
