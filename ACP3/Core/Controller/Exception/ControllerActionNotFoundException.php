<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Core\Controller\Exception;

class ControllerActionNotFoundException extends \Exception implements ForwardControllerActionAwareExceptionInterface
{
    /**
     * {@inheritdoc}
     */
    public function getServiceId(): string
    {
        return 'errors.controller.frontend.index.not_found';
    }

    /**
     * {@inheritdoc}
     */
    public function routeArguments(): array
    {
        return [];
    }
}
