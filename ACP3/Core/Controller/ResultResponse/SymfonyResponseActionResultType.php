<?php
/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENCE file at the top-level module directory for licencing details.
 */

namespace ACP3\Core\Controller\ResultResponse;


use Symfony\Component\HttpFoundation\Response;

class SymfonyResponseActionResultType implements ActionResultTypeInterface
{
    /**
     * @var Response
     */
    private $response;

    /**
     * SymfonyResponseActionResultType constructor.
     * @param Response $response
     */
    public function __construct(Response $response)
    {
        $this->response = $response;
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
        foreach ($this->response->headers->getCookies() as $cookie) {
            $result->headers->setCookie($cookie);
        }
        return $result;
    }
}
