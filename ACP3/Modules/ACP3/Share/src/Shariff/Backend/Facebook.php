<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Share\Shariff\Backend;

/**
 * Class Facebook.
 */
class Facebook extends Request implements ServiceInterface
{
    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'facebook';
    }

    /**
     * {@inheritdoc}
     */
    public function setConfig(array $config)
    {
        if (empty($config['app_id']) || empty($config['secret'])) {
            throw new \InvalidArgumentException('The Facebook app_id and secret must not be empty.');
        }
        parent::setConfig($config);
    }

    /**
     * {@inheritdoc}
     */
    public function getRequest($url)
    {
        $accessToken = urlencode($this->config['app_id']) . '|' . urlencode($this->config['secret']);
        $query = 'https://graph.facebook.com/v7.0/?id=' . urlencode($url) . '&fields=engagement&access_token='
            . $accessToken;

        return new \GuzzleHttp\Psr7\Request('GET', $query);
    }

    /**
     * {@inheritdoc}
     */
    public function extractCount(array $data)
    {
        if (isset(
            $data['engagement']['reaction_count'],
            $data['engagement']['comment_count'],
            $data['engagement']['share_count']
        )) {
            return $data['engagement']['reaction_count']
                + $data['engagement']['comment_count']
                + $data['engagement']['share_count'];
        }

        return 0;
    }
}
