<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Core\Controller;

use ACP3\Core\View;
use Symfony\Component\HttpFoundation\Response;

class DisplayActionTraitImpl
{
    use DisplayActionTrait;

    /**
     * @var Response
     */
    private $response;
    /**
     * @var View
     */
    private $view;

    /**
     * DisplayActionTraitImpl constructor.
     */
    public function __construct(Response $response, View $view)
    {
        $this->response = $response;
        $this->view = $view;
    }

    /**
     * @return string
     */
    protected function applyTemplateAutomatically()
    {
        return 'Foo/Frontend/index.index.tpl';
    }

    protected function addCustomTemplateVarsBeforeOutput()
    {
    }

    /**
     * @return \Symfony\Component\HttpFoundation\Response
     */
    protected function getResponse()
    {
        return $this->response;
    }

    /**
     * @return \ACP3\Core\View
     */
    protected function getView()
    {
        return $this->view;
    }
}
