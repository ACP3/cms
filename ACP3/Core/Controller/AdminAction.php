<?php
/**
 * Copyright (c) 2016 by the ACP3 Developers.
 * See the LICENCE file at the top-level module directory for licencing details.
 */

namespace ACP3\Core\Controller;

use ACP3\Core;

/**
 * Class AdminAction
 * @package ACP3\Core\Controller
 */
abstract class AdminAction extends Core\Controller\FrontendAction
{
    /**
     * @var \ACP3\Core\Session\SessionHandlerInterface
     */
    protected $session;

    /**
     * @param \ACP3\Core\Controller\Context\AdminContext $context
     */
    public function __construct(Context\AdminContext $context)
    {
        parent::__construct($context);

        $this->session = $context->getSession();
    }

    /**
     * @return $this
     * @throws \ACP3\Core\Authentication\Exception\UnauthorizedAccessException
     */
    public function preDispatch()
    {
        if ($this->user->isAuthenticated() === false) {
            throw new Core\Authentication\Exception\UnauthorizedAccessException();
        }

        return parent::preDispatch();
    }
}
