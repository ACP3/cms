<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Users\Controller\Widget\Index;

use ACP3\Core\Controller\AbstractWidgetAction;
use ACP3\Core\Controller\Context\WidgetContext;
use FOS\HttpCache\UserContext\DefaultHashGenerator;
use Symfony\Component\HttpFoundation\AcceptHeader;
use Symfony\Component\HttpFoundation\Response;

class Hash extends AbstractWidgetAction
{
    /**
     * @var DefaultHashGenerator
     */
    private $hashGenerator;

    public function __construct(WidgetContext $context, DefaultHashGenerator $hashGenerator)
    {
        parent::__construct($context);

        $this->hashGenerator = $hashGenerator;
    }

    public function execute(): Response
    {
        $accept = AcceptHeader::fromString($this->request->getSymfonyRequest()->headers->get('Accept'));
        if ($accept->has('application/vnd.fos.user-context-hash')) {
            $this->response->setVary('Cookie');
            $this->response->setPublic();
            $this->response->setMaxAge(3600);
            $this->response->headers->add([
                'Content-type' => 'application/vnd.fos.user-context-hash',
                'X-User-Context-Hash' => $this->hashGenerator->generateHash(),
            ]);
        } else {
            $this->response->setStatusCode(Response::HTTP_NOT_ACCEPTABLE);
        }

        return $this->response;
    }
}
