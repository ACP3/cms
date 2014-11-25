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
     * @var Core\Router\Aliases
     */
    protected $aliases;

    /**
     * @param Frontend $frontendContext
     * @param Core\Validator\Rules\Misc $validate
     * @param Core\Session $session
     * @param Core\Router\Aliases $aliases
     */
    public function __construct(
        Core\Context\Frontend $frontendContext,
        Core\Validator\Rules\Misc $validate,
        Core\Session $session,
        Core\Router\Aliases $aliases
    ) {
        parent::__construct(
            $frontendContext,
            $frontendContext->getAssets(),
            $frontendContext->getBreadcrumb(),
            $frontendContext->getSeo()
        );

        $this->validate = $validate;
        $this->session = $session;
        $this->aliases = $aliases;
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

    public function getAliases()
    {
        return $this->aliases;
    }
}
