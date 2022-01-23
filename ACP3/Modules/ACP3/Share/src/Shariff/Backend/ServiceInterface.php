<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Share\Shariff\Backend;

use Psr\Http\Message\RequestInterface;

interface ServiceInterface
{
    public function getRequest(string $url): RequestInterface;

    /**
     * @param array<string, mixed> $data
     */
    public function extractCount(array $data): int;

    public function getName(): string;

    public function filterResponse(string $content): string;

    /**
     * @param array<string, mixed> $config
     */
    public function setConfig(array $config): void;
}
