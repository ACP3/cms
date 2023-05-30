<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Share\Shariff\Backend;

use Psr\Http\Message\RequestInterface;

class Xing extends Request implements ServiceInterface
{
    public function getName(): string
    {
        return 'xing';
    }

    public function getRequest(string $url): RequestInterface
    {
        return new \GuzzleHttp\Psr7\Request(
            'POST',
            'https://www.xing-share.com/spi/shares/statistics?url=' . urlencode($url)
        );
    }

    public function extractCount(array $data): int
    {
        return $data['share_counter'] ?? 0;
    }
}
