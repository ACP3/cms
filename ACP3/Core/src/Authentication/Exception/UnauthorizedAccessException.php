<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Core\Authentication\Exception;

use ACP3\Core\Controller\Exception\ForwardControllerActionAwareExceptionInterface;

class UnauthorizedAccessException extends \RuntimeException implements ForwardControllerActionAwareExceptionInterface
{
    /**
     * @var array
     */
    private $routeArguments;

    public function __construct(
        array $routeArguments = [],
        string $message = '',
        int $code = 0,
        \Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);

        $this->routeArguments = $routeArguments;
    }

    /**
     * Returns the serviceId of the controller action to forward to.
     *
     * @return string
     */
    public function getServiceId(): string
    {
        return 'users.controller.frontend.index.login';
    }

    /**
     * @return array
     */
    public function routeParams(): array
    {
        return $this->routeArguments;
    }
}
