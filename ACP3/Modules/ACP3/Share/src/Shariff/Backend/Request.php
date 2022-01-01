<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Share\Shariff\Backend;

use GuzzleHttp\ClientInterface;

abstract class Request
{
    /** @var array */
    protected $config;

    public function __construct(protected ClientInterface $client)
    {
    }

    public function filterResponse($content)
    {
        return $content;
    }

    public function setConfig(array $config): void
    {
        $this->config = $config;
    }
}
