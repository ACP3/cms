<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Core\Controller\Context;

use ACP3\Core;

class FrontendContext extends Core\Controller\Context\WidgetContext
{
    /**
     * @var \ACP3\Core\Breadcrumb\Steps
     */
    private $breadcrumb;
    /**
     * @var \ACP3\Core\Breadcrumb\Title
     */
    private $title;
    /**
     * @var \ACP3\Core\Modules\Helper\Action
     */
    private $actionHelper;
    /**
     * @var \ACP3\Core\Helpers\RedirectMessages
     */
    private $redirectMessagesHelper;

    public function __construct(
        Core\Controller\Context\WidgetContext $context,
        Core\Breadcrumb\Steps $breadcrumb,
        Core\Breadcrumb\Title $title,
        Core\Modules\Helper\Action $actionHelper,
        Core\Helpers\RedirectMessages $redirectMessagesHelper
    ) {
        parent::__construct(
            $context->getContainer(),
            $context->getEventDispatcher(),
            $context->getUser(),
            $context->getTranslator(),
            $context->getRequest(),
            $context->getView(),
            $context->getConfig(),
            $context->getAppPath(),
            $context->getResponse()
        );

        $this->breadcrumb = $breadcrumb;
        $this->title = $title;
        $this->actionHelper = $actionHelper;
        $this->redirectMessagesHelper = $redirectMessagesHelper;
    }

    /**
     * @return \ACP3\Core\Breadcrumb\Steps
     */
    public function getBreadcrumb()
    {
        return $this->breadcrumb;
    }

    /**
     * @return \ACP3\Core\Breadcrumb\Title
     */
    public function getTitle()
    {
        return $this->title;
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
}
