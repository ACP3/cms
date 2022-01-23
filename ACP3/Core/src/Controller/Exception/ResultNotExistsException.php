<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Core\Controller\Exception;

use Symfony\Component\HttpFoundation\Response;
use Throwable;

class ResultNotExistsException extends \RuntimeException implements ForwardControllerActionAwareExceptionInterface
{
    public function __construct(string $message = '', Throwable $previous = null)
    {
        parent::__construct($message, Response::HTTP_NOT_FOUND, $previous);
    }

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
    public function routeParams(): array
    {
        return [];
    }
}
