<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Share\Shariff\Backend;

use Psr\Http\Message\RequestInterface;

class Pinterest extends Request implements ServiceInterface
{
    /**
     * {@inheritdoc}
     */
    public function getName(): string
    {
        return 'pinterest';
    }

    /**
     * {@inheritdoc}
     */
    public function getRequest(string $url): RequestInterface
    {
        return new \GuzzleHttp\Psr7\Request(
            'GET',
            'https://api.pinterest.com/v1/urls/count.json?callback=x&url=' . urlencode($url)
        );
    }

    /**
     * {@inheritdoc}
     */
    public function filterResponse(string $content): string
    {
        return mb_substr($content, 2, -1);
    }

    /**
     * {@inheritdoc}
     */
    public function extractCount(array $data): int
    {
        return $data['count'] ?? 0;
    }
}
