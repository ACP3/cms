<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Share\Shariff\Backend;

use Psr\Http\Message\RequestInterface;

/**
 * Interface ServiceInterface.
 */
interface ServiceInterface
{
    public function getRequest(string $url): RequestInterface;

    public function extractCount(array $data): int;

    public function getName(): string;

    /**
     * @param string $content
     *
     * @return string
     */
    public function filterResponse($content);

    public function setConfig(array $config): void;
}
