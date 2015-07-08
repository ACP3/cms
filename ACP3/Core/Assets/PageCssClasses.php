<?php
namespace ACP3\Core\Assets;

use ACP3\Core;

/**
 * Class PageCssClasses
 * @package ACP3\Core\Assets
 */
class PageCssClasses
{
    /**
     * @var \ACP3\Core\Breadcrumb
     */
    protected $breadcrumb;
    /**
     * @var \ACP3\Core\Http\RequestInterface
     */
    protected $request;

    /**
     * @var string
     */
    protected $module;
    /**
     * @var string
     */
    protected $controllerAction;
    /**
     * @var string
     */
    protected $details;

    /**
     * @param \ACP3\Core\Breadcrumb       $breadcrumb
     * @param \ACP3\Core\Http\RequestInterface $request
     */
    public function __construct(
        Core\Breadcrumb $breadcrumb,
        Core\Http\RequestInterface $request
    )
    {
        $this->breadcrumb = $breadcrumb;
        $this->request = $request;
    }

    /**
     * @return string
     */
    public function getModule()
    {
        if ($this->module === null) {
            $this->module = $this->request->getModule();
        }
        return $this->module;
    }

    /**
     * @return string
     */
    public function getControllerAction()
    {
        if ($this->controllerAction === null) {
            $this->controllerAction = $this->request->getModule() . '-' . $this->request->getController() . '-' . $this->request->getControllerAction();
        }
        return $this->controllerAction;
    }

    /**
     * @return string
     */
    public function getDetails()
    {
        if ($this->details === null) {
            $pageTitle = preg_replace(
                '=[^a-z0-9\-]=',
                '',
                \Patchwork\Utf8::toAscii(
                    html_entity_decode(
                        str_replace(
                            ' ',
                            '-',
                            strtolower($this->breadcrumb->getPageTitle())
                        ),
                        ENT_QUOTES,
                        'UTF-8'
                    )
                )
            );
            $this->details = $this->request->getModule() . '-' . $this->request->getController() . '-' . $pageTitle;
        }

        return $this->details;
    }
}