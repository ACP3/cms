<?php
namespace ACP3\Core\Modules\Controller;

use ACP3\Core;

/**
 * Class AdminContext
 * @package ACP3\Core\Modules\Controller
 */
class AdminContext extends FrontendContext
{
    /**
     * @var \ACP3\Core\SessionHandler
     */
    protected $session;

    /**
     * @param \ACP3\Core\Modules\Controller\FrontendContext $frontendContext
     * @param \ACP3\Core\SessionHandler                     $session
     */
    public function __construct(
        Core\Modules\Controller\FrontendContext $frontendContext,
        Core\SessionHandler $session
    )
    {
        parent::__construct(
            $frontendContext,
            $frontendContext->getAssets(),
            $frontendContext->getBreadcrumb(),
            $frontendContext->getSeo(),
            $frontendContext->getActionHelper(),
            $frontendContext->getResponse()
        );

        $this->session = $session;
    }

    /**
     * @return \ACP3\Core\SessionHandler
     */
    public function getSession()
    {
        return $this->session;
    }
}
