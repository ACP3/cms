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
     * @var string
     */
    protected $details;

    public function __construct(protected Core\Helpers\StringFormatter $stringFormatter, protected Core\Breadcrumb\Title $title, protected Core\Http\RequestInterface $request)
    {
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
