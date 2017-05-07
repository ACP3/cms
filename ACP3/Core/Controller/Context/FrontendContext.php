<?php
/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENCE file at the top-level module directory for licencing details.
 */

namespace ACP3\Core\Controller\Context;

use ACP3\Core;

class FrontendContext extends Core\Controller\Context\WidgetContext
{
    /**
     * @var \ACP3\Core\Modules\Helper\Action
     */
    private $actionHelper;

    /**
     * @param \ACP3\Core\Controller\Context\WidgetContext $context
     * @param \ACP3\Core\Modules\Helper\Action            $actionHelper
     */
    public function __construct(
        Core\Controller\Context\WidgetContext $context,
        Core\Modules\Helper\Action $actionHelper
    ) {
        parent::__construct(
            $context->getContainer(),
            $context->getEventDispatcher(),
            $context->getUser(),
            $context->getTranslator(),
            $context->getModules(),
            $context->getRequest(),
            $context->getRouter(),
            $context->getView(),
            $context->getConfig(),
            $context->getAppPath(),
            $context->getResponse(),
            $context->getActionResultFactory()
        );

        $this->actionHelper = $actionHelper;
    }

    /**
     * @return Core\Modules\Helper\Action
     */
    public function getActionHelper()
    {
        return $this->actionHelper;
    }
}
