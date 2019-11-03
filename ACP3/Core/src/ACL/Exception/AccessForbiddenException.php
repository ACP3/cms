<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Core\ACL\Exception;

use ACP3\Core\Controller\Exception\ForwardControllerActionAwareExceptionInterface;

class AccessForbiddenException extends \RuntimeException implements ForwardControllerActionAwareExceptionInterface
{
    /**
     * {@inheritdoc}
     */
    public function getServiceId(): string
    {
        return 'errors.controller.frontend.index.access_forbidden';
    }

    /**
     * {@inheritdoc}
     */
    public function routeParams(): array
    {
        return [];
    }
}
