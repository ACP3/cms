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
     * @var \ACP3\Core\Breadcrumb\Steps
     */
    protected $breadcrumb;
    /**
     * @var \ACP3\Core\Breadcrumb\Title
     */
    protected $title;
    /**
     * @var \ACP3\Core\Http\RequestInterface
     */
    protected $request;

    /**
     * @var string
     */
    protected $details;

    /**
     * @param \ACP3\Core\Breadcrumb\Steps      $breadcrumb
     * @param \ACP3\Core\Breadcrumb\Title      $title
     * @param \ACP3\Core\Http\RequestInterface $request
     */
    public function __construct(
        Core\Breadcrumb\Steps $breadcrumb,
        Core\Breadcrumb\Title $title,
        Core\Http\RequestInterface $request
    ) {
        $this->breadcrumb = $breadcrumb;
        $this->title = $title;
        $this->request = $request;
    }

    /**
     * @return string
     */
    public function getModule()
    {
        return $this->request->getModule();
    }

    /**
     * @return string
     */
    public function getControllerAction()
    {
        return $this->request->getModule() . '-' . $this->request->getController() . '-' . $this->request->getAction();
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
                            strtolower($this->title->getPageTitle())
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
