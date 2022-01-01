<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Core\Authentication\Exception;

use ACP3\Core\Controller\Exception\ForwardControllerActionAwareExceptionInterface;
use Symfony\Component\HttpFoundation\Response;

class UnauthorizedAccessException extends \RuntimeException implements ForwardControllerActionAwareExceptionInterface
{
    public function __construct(
        private array $routeArguments = [],
        string $message = '',
        \Throwable $previous = null)
    {
        parent::__construct($message, Response::HTTP_UNAUTHORIZED, $previous);
    }

    /**
     * Returns the serviceId of the controller action to forward to.
     */
    public function getServiceId(): string
    {
        return 'users.controller.frontend.index.login';
    }

    public function routeParams(): array
    {
        return $this->routeArguments;
    }
}
