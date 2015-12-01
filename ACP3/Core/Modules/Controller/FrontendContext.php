<?php
namespace ACP3\Core\Modules\Controller;

use ACP3\Core;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class FrontendContext
 * @package ACP3\Core\Modules\Controller
 */
class FrontendContext extends Core\Modules\Controller\Context
{
    /**
     * @var \ACP3\Core\Assets
     */
    protected $assets;
    /**
     * @var \ACP3\Core\Breadcrumb
     */
    protected $breadcrumb;
    /**
     * @var \ACP3\Core\SEO
     */
    protected $seo;
    /**
     * @var \ACP3\Core\Modules\Helper\Action
     */
    protected $actionHelper;
    /**
     * @var \Symfony\Component\HttpFoundation\Response
     */
    protected $response;

    /**
     * @param \ACP3\Core\Modules\Controller\Context      $context
     * @param \ACP3\Core\Assets                          $assets
     * @param \ACP3\Core\Breadcrumb                      $breadcrumb
     * @param \ACP3\Core\SEO                             $seo
     * @param \ACP3\Core\Modules\Helper\Action           $actionHelper
     * @param \Symfony\Component\HttpFoundation\Response $response
     */
    public function __construct(
        Core\Modules\Controller\Context $context,
        Core\Assets $assets,
        Core\Breadcrumb $breadcrumb,
        Core\SEO $seo,
        Core\Modules\Helper\Action $actionHelper,
        Response $response
    )
    {
        parent::__construct(
            $context->getEventDispatcher(),
            $context->getACL(),
            $context->getUser(),
            $context->getLang(),
            $context->getModules(),
            $context->getRequest(),
            $context->getRouter(),
            $context->getValidator(),
            $context->getView(),
            $context->getConfig()
        );

        $this->assets = $assets;
        $this->breadcrumb = $breadcrumb;
        $this->seo = $seo;
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
     * @return \ACP3\Core\Breadcrumb
     */
    public function getBreadcrumb()
    {
        return $this->breadcrumb;
    }

    /**
     * @return \ACP3\Core\SEO
     */
    public function getSeo()
    {
        return $this->seo;
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
