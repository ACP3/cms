<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Core\Controller\Context;

use ACP3\Core;

class FormContext extends FrontendContext
{
    /**
     * @var \ACP3\Core\Modules\Helper\Action
     */
    private $actionHelper;
    /**
     * @var \ACP3\Core\Helpers\RedirectMessages
     */
    private $redirectMessagesHelper;
    /**
     * @var \ACP3\Core\Http\RedirectResponse
     */
    private $redirectResponse;

    /**
     * FormContext constructor.
     *
     * @param \ACP3\Core\Controller\Context\FrontendContext $context
     * @param \ACP3\Core\Modules\Helper\Action              $actionHelper
     * @param \ACP3\Core\Helpers\RedirectMessages           $redirectMessagesHelper
     * @param \ACP3\Core\Http\RedirectResponse              $redirectResponse
     */
    public function __construct(
        Core\Controller\Context\FrontendContext $context,
        Core\Modules\Helper\Action $actionHelper,
        Core\Helpers\RedirectMessages $redirectMessagesHelper,
        Core\Http\RedirectResponse $redirectResponse
    ) {
        parent::__construct(
            $context,
            $context->getBreadcrumb(),
            $context->getTitle()
        );

        $this->actionHelper = $actionHelper;
        $this->redirectMessagesHelper = $redirectMessagesHelper;
        $this->redirectResponse = $redirectResponse;
    }

    /**
     * @return Core\Modules\Helper\Action
     */
    public function getActionHelper()
    {
        return $this->actionHelper;
    }

    /**
     * @return \ACP3\Core\Helpers\RedirectMessages
     */
    public function getRedirectMessagesHelper()
    {
        return $this->redirectMessagesHelper;
    }

    /**
     * @return \ACP3\Core\Http\RedirectResponse
     */
    public function getRedirectResponse()
    {
        return $this->redirectResponse;
    }
}
