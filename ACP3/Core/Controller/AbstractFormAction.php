<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Core\Controller;

class AbstractFormAction extends AbstractFrontendAction
{
    /**
     * @var \ACP3\Core\Modules\Helper\Action
     */
    protected $actionHelper;
    /**
     * @return \ACP3\Core\Helpers\RedirectMessages
     */
    private $redirectMessages;
    /**
     * @var \ACP3\Core\Http\RedirectResponse
     */
    private $redirectResponse;

    public function __construct(Context\FormContext $context)
    {
        parent::__construct($context);

        $this->actionHelper = $context->getActionHelper();
        $this->redirectMessages = $context->getRedirectMessagesHelper();
        $this->redirectResponse = $context->getRedirectResponse();
    }

    /**
     * @return \ACP3\Core\Helpers\RedirectMessages
     */
    public function redirectMessages()
    {
        return $this->redirectMessages;
    }

    /**
     * @return \ACP3\Core\Http\RedirectResponse
     */
    public function redirect()
    {
        return $this->redirectResponse;
    }
}
