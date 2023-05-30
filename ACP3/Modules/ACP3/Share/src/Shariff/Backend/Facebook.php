<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Share\Shariff\Backend;

use Psr\Http\Message\RequestInterface;

class Facebook extends Request implements ServiceInterface
{
    public function getName(): string
    {
        return 'facebook';
    }

    public function setConfig(array $config): void
    {
        if (empty($config['app_id']) || empty($config['secret'])) {
            throw new \InvalidArgumentException('The Facebook app_id and secret must not be empty.');
        }
        parent::setConfig($config);
    }

    public function getRequest(string $url): RequestInterface
    {
        $accessToken = urlencode((string) $this->config['app_id']) . '|' . urlencode((string) $this->config['secret']);
        $query = 'https://graph.facebook.com/v12.0/?id=' . urlencode($url) . '&fields=og_object%7Bengagement%7D&access_token='
            . $accessToken;

        return new \GuzzleHttp\Psr7\Request('GET', $query);
    }

    public function extractCount(array $data): int
    {
        return $data['og_object']['engagement']['count'] ?? 0;
    }
}
