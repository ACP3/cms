<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Core\Assets;

use ACP3\Core;

class PageCssClasses
{
    /**
     * @var Core\Helpers\StringFormatter
     */
    protected $stringFormatter;
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
     * @param \ACP3\Core\Breadcrumb\Title      $title
     * @param \ACP3\Core\Http\RequestInterface $request
     */
    public function __construct(
        Core\Helpers\StringFormatter $stringFormatter,
        Core\Breadcrumb\Title $title,
        Core\Http\RequestInterface $request
    ) {
        $this->title = $title;
        $this->request = $request;
        $this->stringFormatter = $stringFormatter;
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
            $pageTitle = $this->stringFormatter->makeStringUrlSafe($this->title->getPageTitle());
            $this->details = $this->request->getModule() . '-' . $this->request->getController() . '-' . $pageTitle;
        }

        return $this->details;
    }
}
