<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Core\ACL\Exception;

use ACP3\Core\Controller\Exception\ForwardControllerActionAwareExceptionInterface;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

class AccessForbiddenException extends \RuntimeException implements ForwardControllerActionAwareExceptionInterface
{
    public function __construct($message = '', Throwable $previous = null)
    {
        parent::__construct($message, Response::HTTP_FORBIDDEN, $previous);
    }

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
