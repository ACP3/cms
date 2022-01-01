<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Share\Controller\Frontend\Index;

use ACP3\Core\Controller\AbstractWidgetAction;
use ACP3\Core\Controller\Context\WidgetContext;
use ACP3\Modules\ACP3\Share\Shariff\BackendManager;
use Symfony\Component\HttpFoundation\JsonResponse;

class Index extends AbstractWidgetAction
{
    public function __construct(
        WidgetContext $context,
        private BackendManager $shariffBackend
    ) {
        parent::__construct($context);
    }

    public function __invoke(): JsonResponse
    {
        return new JsonResponse(
            $this->shariffBackend->get(
                $this->request->getSymfonyRequest()->query->get('url', '')
            )
        );
    }
}
