<?php
/**
 * Copyright (c) 2016 by the ACP3 Developers.
 * See the LICENCE file at the top-level module directory for licencing details.
 */

namespace ACP3\Core\Controller;

use ACP3\Core;

/**
 * Class AbstractAdminAction
 * @package ACP3\Core\Controller
 */
abstract class AbstractAdminAction extends Core\Controller\AbstractFrontendAction
{
    /**
     * @return $this
     * @throws \ACP3\Core\Authentication\Exception\UnauthorizedAccessException
     */
    public function preDispatch()
    {
        if ($this->user->isAuthenticated() === false) {
            throw new Core\Authentication\Exception\UnauthorizedAccessException();
        }

        parent::preDispatch();

        return $this;
    }
}
