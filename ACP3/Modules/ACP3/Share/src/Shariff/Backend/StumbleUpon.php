<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Share\Shariff\Backend;

use Psr\Http\Message\RequestInterface;

class StumbleUpon extends Request implements ServiceInterface
{
    /**
     * {@inheritdoc}
     */
    public function getName(): string
    {
        return 'stumbleupon';
    }

    /**
     * {@inheritdoc}
     */
    public function getRequest(string $url): RequestInterface
    {
        return new \GuzzleHttp\Psr7\Request(
            'GET',
            'https://www.stumbleupon.com/services/1.01/badge.getinfo?url=' . urlencode($url)
        );
    }

    /**
     * {@inheritdoc}
     */
    public function extractCount(array $data): int
    {
        return (isset($data['result']['views'])) ? $data['result']['views'] + 0 : 0;
    }
}
