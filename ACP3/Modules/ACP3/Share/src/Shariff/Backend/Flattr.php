<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Share\Shariff\Backend;

use Psr\Http\Message\RequestInterface;

class Flattr extends Request implements ServiceInterface
{
    public function getName(): string
    {
        return 'flattr';
    }

    public function getRequest(string $url): RequestInterface
    {
        return new \GuzzleHttp\Psr7\Request(
            'GET',
            'https://api.flattr.com/rest/v2/things/lookup/?url=' . urlencode($url)
        );
    }

    public function extractCount(array $data): int
    {
        return $data['flattrs'] ?? 0;
    }
}
