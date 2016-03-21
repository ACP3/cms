<?php
/**
 * Copyright (c) 2016 by the ACP3 Developers. See the LICENCE file at the top-level module directory for licencing details.
 */

namespace ACP3\Core\Controller\Context;

use ACP3\Core;

/**
 * Class AdminContext
 * @package ACP3\Core\Controller\Context
 */
class AdminContext extends FrontendContext
{
    /**
     * @var \ACP3\Core\Session\SessionHandlerInterface
     */
    protected $session;

    /**
     * AdminContext constructor.
     *
     * @param \ACP3\Core\Controller\Context\FrontendContext $context
     * @param \ACP3\Core\Session\SessionHandlerInterface    $session
     */
    public function __construct(
        Core\Controller\Context\FrontendContext $context,
        Core\Session\SessionHandlerInterface $session
    ) {
        parent::__construct(
            $context,
            $context->getAssets(),
            $context->getBreadcrumb(),
            $context->getSeo(),
            $context->getActionHelper(),
            $context->getResponse()
        );

        $this->session = $session;
    }

    /**
     * @return \ACP3\Core\Session\SessionHandlerInterface
     */
    public function getSession()
    {
        return $this->session;
    }
}
