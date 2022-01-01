<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Share\Shariff\Backend;

abstract class Request
{
    protected array $config = [];

    public function filterResponse(string $content): string
    {
        return $content;
    }

    public function setConfig(array $config): void
    {
        $this->config = $config;
    }
}
