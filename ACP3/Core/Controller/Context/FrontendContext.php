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

    public function __construct(
        Core\Controller\Context\WidgetContext $context,
        Core\Breadcrumb\Steps $breadcrumb,
        Core\Breadcrumb\Title $title
    ) {
        parent::__construct(
            $context->getContainer(),
            $context->getEventDispatcher(),
            $context->getACL(),
            $context->getUser(),
            $context->getTranslator(),
            $context->getModules(),
            $context->getRequest(),
            $context->getView(),
            $context->getConfig(),
            $context->getAppPath(),
            $context->getResponse()
        );

        $this->breadcrumb = $breadcrumb;
        $this->title = $title;
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
}
