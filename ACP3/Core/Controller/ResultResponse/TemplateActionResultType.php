<?php
/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENCE file at the top-level module directory for licencing details.
 */

namespace ACP3\Core\Controller\ResultResponse;


use ACP3\Core\Http\RequestInterface;
use ACP3\Core\View;
use Symfony\Component\HttpFoundation\Response;

class TemplateActionResultType implements ActionResultTypeInterface
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
        return is_string($result) && $this->view->templateExists($result);
    }

    /**
     * @inheritdoc
     */
    public function process($result): Response
    {
        $this->view->setTemplate($result);

        $this->response->setContent($this->view->fetchTemplate($this->view->getTemplate()));

        return $this->response;
    }
}
