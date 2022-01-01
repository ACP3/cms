<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Share\Shariff\Backend;

use Psr\Http\Message\RequestInterface;

class Vk extends Request implements ServiceInterface
{
    /**
     * {@inheritdoc}
     */
    public function getName(): string
    {
        return 'vk';
    }

    /**
     * {@inheritdoc}
     */
    public function getRequest(string $url): RequestInterface
    {
        return new \GuzzleHttp\Psr7\Request(
            'GET',
            'https://vk.com/share.php?act=count&index=1&url=' . urlencode($url)
        );
    }

    /**
     * {@inheritdoc}
     */
    public function filterResponse(string $content): string
    {
        // 'VK.Share.count(1, x);' with x being the count
        $strCount = mb_substr($content, 18, -2);

        return $strCount !== '' ? '{"count": ' . $strCount . '}' : '';
    }

    /**
     * {@inheritdoc}
     */
    public function extractCount(array $data): int
    {
        return $data['count'] ?? 0;
    }
}
