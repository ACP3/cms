<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Users\Controller\Widget\Index;

use ACP3\Core\Controller\AbstractWidgetAction;
use ACP3\Core\Controller\Context\Context;
use FOS\HttpCache\UserContext\DefaultHashGenerator;
use Symfony\Component\HttpFoundation\AcceptHeader;
use Symfony\Component\HttpFoundation\Response;

class Hash extends AbstractWidgetAction
{
    public function __construct(Context $context, private readonly DefaultHashGenerator $hashGenerator)
    {
        parent::__construct($context);
    }

    public function __invoke(): Response
    {
        $response = new Response();
        $accept = AcceptHeader::fromString($this->request->getSymfonyRequest()->headers->get('Accept'));
        if ($accept->has('application/vnd.fos.user-context-hash')) {
            $response->setVary('Cookie, Authorization');
            $response->setMaxAge(3600);
            $response->headers->add([
                'Content-type' => 'application/vnd.fos.user-context-hash',
                'X-User-Context-Hash' => $this->hashGenerator->generateHash(),
            ]);
        } else {
            $response->setStatusCode(Response::HTTP_NOT_ACCEPTABLE);
        }

        return $response;
    }
}
