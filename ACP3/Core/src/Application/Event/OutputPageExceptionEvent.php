<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Core\Application\Event;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Contracts\EventDispatcher\Event;

class OutputPageExceptionEvent extends Event
{
    public const NAME = 'core.output_page_exception';

    /**
     * @var \Symfony\Component\HttpFoundation\Response
     */
    private $response;

    public function __construct(private readonly \Throwable $throwable)
    {
    }

    public function getThrowable(): \Throwable
    {
        return $this->throwable;
    }

    public function getResponse(): Response
    {
        return $this->response;
    }

    public function hasResponse(): bool
    {
        return $this->response !== null;
    }

    public function setResponse(Response $response): void
    {
        $this->response = $response;
    }
}
