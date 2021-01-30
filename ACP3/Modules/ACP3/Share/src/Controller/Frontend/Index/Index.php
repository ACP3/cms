<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Share\Controller\Frontend\Index;

use ACP3\Core\Controller\AbstractFrontendAction;
use ACP3\Core\Controller\Context\WidgetContext;
use Heise\Shariff\Backend;
use Symfony\Component\HttpFoundation\JsonResponse;

class Index extends AbstractFrontendAction
{
    /**
     * @var \Heise\Shariff\Backend
     */
    private $shariffBackend;

    public function __construct(
        WidgetContext $context,
        Backend $shariffBackend
    ) {
        parent::__construct($context);

        $this->shariffBackend = $shariffBackend;
    }

    public function execute(): JsonResponse
    {
        return new JsonResponse(
            $this->shariffBackend->get(
                $this->request->getSymfonyRequest()->query->get('url', '')
            )
        );
    }
}
