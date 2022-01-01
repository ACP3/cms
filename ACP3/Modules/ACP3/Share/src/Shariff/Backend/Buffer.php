<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Share\Shariff\Backend;

use Psr\Http\Message\RequestInterface;

class Buffer extends Request implements ServiceInterface
{
    /**
     * {@inheritdoc}
     */
    public function getName(): string
    {
        return 'buffer';
    }

    /**
     * {@inheritdoc}
     */
    public function getRequest(string $url): RequestInterface
    {
        return new \GuzzleHttp\Psr7\Request(
            'GET',
            'https://api.bufferapp.com/1/links/shares.json?url=' . urlencode($url)
        );
    }

    /**
     * {@inheritdoc}
     */
    public function extractCount(array $data): int
    {
        return $data['shares'] ?? 0;
    }
}
