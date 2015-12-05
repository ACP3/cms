<?php

namespace ACP3\Core\Modules;

use ACP3\Core;

/**
 * Class AdminController
 * @package ACP3\Core\Modules
 */
abstract class AdminController extends Core\Modules\FrontendController
{
    /**
     * @var \ACP3\Core\SessionHandler
     */
    protected $session;

    /**
     * @param \ACP3\Core\Modules\Controller\AdminContext $adminContext
     */
    public function __construct(Controller\AdminContext $adminContext)
    {
        parent::__construct($adminContext);

        $this->session = $adminContext->getSession();
    }

    /**
     * @return $this
     * @throws \ACP3\Core\Exceptions\UnauthorizedAccess
     */
    public function preDispatch()
    {
        if ($this->user->isAuthenticated() === false) {
            throw new Core\Exceptions\UnauthorizedAccess();
        }

        return parent::preDispatch();
    }
}
