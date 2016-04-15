<?php
/**
 * Copyright (c) 2016 by the ACP3 Developers.
 * See the LICENCE file at the top-level module directory for licencing details.
 */

namespace ACP3\Core\Controller\Context;

use ACP3\Core;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class FrontendContext
 * @package ACP3\Core\Controller\Context
 */
class FrontendContext extends Core\Controller\Context\WidgetContext
{
    /**
     * @var \ACP3\Core\Assets
     */
    private $assets;
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
     * @var \Symfony\Component\HttpFoundation\Response
     */
    private $response;

    /**
     * @param \ACP3\Core\Controller\Context\WidgetContext $context
     * @param \ACP3\Core\Assets                           $assets
     * @param \ACP3\Core\Breadcrumb\Steps                 $breadcrumb
     * @param \ACP3\Core\Breadcrumb\Title                 $title
     * @param \ACP3\Core\Modules\Helper\Action            $actionHelper
     * @param \Symfony\Component\HttpFoundation\Response  $response
     */
    public function __construct(
        Core\Controller\Context\WidgetContext $context,
        Core\Assets $assets,
        Core\Breadcrumb\Steps $breadcrumb,
        Core\Breadcrumb\Title $title,
        Core\Modules\Helper\Action $actionHelper,
        Response $response
    ) {
        parent::__construct(
            $context->getContainer(),
            $context->getEventDispatcher(),
            $context->getACL(),
            $context->getUser(),
            $context->getTranslator(),
            $context->getModules(),
            $context->getRequest(),
            $context->getRouter(),
            $context->getValidator(),
            $context->getView(),
            $context->getConfig(),
            $context->getAppPath()
        );

        $this->assets = $assets;
        $this->breadcrumb = $breadcrumb;
        $this->title = $title;
        $this->actionHelper = $actionHelper;
        $this->response = $response;
    }

    /**
     * @return Core\Assets
     */
    public function getAssets()
    {
        return $this->assets;
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
     * @return Response
     */
    public function getResponse()
    {
        return $this->response;
    }
}
