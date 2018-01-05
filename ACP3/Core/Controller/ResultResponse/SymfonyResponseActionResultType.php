<?php
/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENCE file at the top-level module directory for licencing details.
 */

namespace ACP3\Core\Controller\ResultResponse;

use ACP3\Core\Http\RequestInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;

class SymfonyResponseActionResultType implements ActionResultTypeInterface
{
    /**
     * @var Response
     */
    private $response;
    /**
     * @var RequestInterface
     */
    private $request;

    /**
     * SymfonyResponseActionResultType constructor.
     * @param Response $response
     * @param RequestInterface $request
     */
    public function __construct(Response $response, RequestInterface $request)
    {
        $this->response = $response;
        $this->request = $request;
    }

    /**
     * @inheritdoc
     */
    public function supports($result): bool
    {
        return $result instanceof Response;
    }

    /**
     * @inheritdoc
     */
    public function process($result): Response
    {
        if ($result instanceof RedirectResponse && $this->request->isXmlHttpRequest()) {
            $result = new JsonResponse(['redirect_url' => $result->getTargetUrl()]);
        }

        foreach ($this->response->headers->getCookies() as $cookie) {
            $result->headers->setCookie($cookie);
        }

        return $result;
    }
}
