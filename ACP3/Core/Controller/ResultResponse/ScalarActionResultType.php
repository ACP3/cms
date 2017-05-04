<?php
/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENCE file at the top-level module directory for licencing details.
 */

namespace ACP3\Core\Controller\ResultResponse;


use ACP3\Core\Http\RequestInterface;
use ACP3\Core\View;
use Symfony\Component\HttpFoundation\Response;

class ScalarActionResultType extends ArrayActionResultType
{
    /**
     * @var View
     */
    private $view;
    /**
     * @var Response
     */
    private $response;

    /**
     * ScalarActionResultType constructor.
     * @param View $view
     * @param RequestInterface $request
     * @param Response $response
     */
    public function __construct(View $view, RequestInterface $request, Response $response)
    {
        parent::__construct($view, $request, $response);

        $this->view = $view;
        $this->response = $response;
    }

    /**
     * @inheritdoc
     */
    public function supports($result): bool
    {
        return is_scalar($result) || $result === null;
    }

    /**
     * @inheritdoc
     */
    public function process($result): Response
    {
        if (empty($result) && $result !== false) {
            // @TODO Get the following code to work, so that we can get rid of most $this->view->setTemplate occurrences
//            if (is_string($result) && $this->view->templateExists($result)) {
//                $this->view->setTemplate($result);
//            }

            return parent::process($result);
        }

        $this->response->setContent($result === false ? '' : $result);

        return $this->response;
    }
}
