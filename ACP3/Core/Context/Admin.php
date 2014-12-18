<?php
namespace ACP3\Core\Context;

use ACP3\Core;

/**
 * Class Admin
 * @package ACP3\Core\Context
 */
class Admin extends Frontend
{
    /**
     * @var \ACP3\Core\Validator\Rules\Misc
     */
    protected $validate;
    /**
     * @var \ACP3\Core\Session
     */
    protected $session;

    /**
     * @param \ACP3\Core\Context\Frontend     $frontendContext
     * @param \ACP3\Core\Validator\Rules\Misc $validate
     * @param \ACP3\Core\Session              $session
     */
    public function __construct(
        Core\Context\Frontend $frontendContext,
        Core\Validator\Rules\Misc $validate,
        Core\Session $session
    ) {
        parent::__construct(
            $frontendContext,
            $frontendContext->getAssets(),
            $frontendContext->getBreadcrumb(),
            $frontendContext->getSeo()
        );

        $this->validate = $validate;
        $this->session = $session;
    }

    /**
     * @return \ACP3\Core\Session
     */
    public function getSession()
    {
        return $this->session;
    }

    /**
     * @return \ACP3\Core\Validator\Rules\Misc
     */
    public function getValidate()
    {
        return $this->validate;
    }
}
