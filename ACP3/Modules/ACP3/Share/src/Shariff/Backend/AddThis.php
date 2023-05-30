<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Share\Shariff\Backend;

use Psr\Http\Message\RequestInterface;

class AddThis extends Request implements ServiceInterface
{
    public function getName(): string
    {
        return 'addthis';
    }

    public function getRequest(string $url): RequestInterface
    {
        return new \GuzzleHttp\Psr7\Request(
            'GET',
            'https://api-public-oci-origin.addthis.com/url/shares.json?url=' . urlencode($url)
        );
    }

    public function extractCount(array $data): int
    {
        return $data['shares'] ?? 0;
    }
}
