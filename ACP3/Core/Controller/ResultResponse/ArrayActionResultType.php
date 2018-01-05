<?php
/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENCE file at the top-level module directory for licencing details.
 */

namespace ACP3\Core\Controller\ResultResponse;

use ACP3\Core\Http\RequestInterface;
use ACP3\Core\View;
use Symfony\Component\HttpFoundation\Response;

class ArrayActionResultType implements ActionResultTypeInterface
{
    /**
     * @var View
     */
    private $view;
    /**
     * @var RequestInterface
     */
    private $request;
    /**
     * @var Response
     */
    private $response;

    /**
     * ArrayActionResultType constructor.
     * @param View $view
     * @param RequestInterface $request
     * @param Response $response
     */
    public function __construct(View $view, RequestInterface $request, Response $response)
    {
        $this->view = $view;
        $this->request = $request;
        $this->response = $response;
    }

    /**
     * @inheritdoc
     */
    public function supports($result): bool
    {
        return \is_array($result);
    }

    /**
     * @inheritdoc
     */
    public function process($result): Response
    {
        $this->view->assign($result);

        if ($this->view->getTemplate() === '') {
            $this->view->setTemplate($this->applyTemplateFromRequest());
        }

        $this->response->setContent($this->view->fetchTemplate($this->view->getTemplate()));

        return $this->response;
    }

    /**
     * @return string
     */
    private function applyTemplateFromRequest(): string
    {
        return $this->request->getModule() . '/'
            . \ucfirst($this->request->getArea()) . '/'
            . $this->request->getController() . '.'
            . $this->request->getAction() . '.tpl';
    }
}
