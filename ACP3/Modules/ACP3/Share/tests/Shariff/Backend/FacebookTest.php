<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Share\Shariff\Backend;

use GuzzleHttp\ClientInterface;
use PHPUnit\Framework as PHPUnit;

/**
 * Class FacebookTest.
 */
class FacebookTest extends PHPUnit\TestCase
{
    public function testConfig()
    {
        /** @var ClientInterface|PHPUnit\MockObject\MockObject $client */
        $client = $this->getMockBuilder(ClientInterface::class)->getMock();

        $facebook = new Facebook($client);
        $facebook->setConfig(['app_id' => 'foo', 'secret' => 'bar']);
        $request = $facebook->getRequest('http://www.heise.de');
        $this->assertEquals(
            'id=' . urlencode('http://www.heise.de') . '&fields=engagement&access_token=foo%7Cbar',
            $request->getUri()->getQuery()
        );
    }

    public function testUsesGraphApi()
    {
        /** @var \GuzzleHttp\Client|PHPUnit\MockObject\MockObject $client */
        $client = $this->getMockBuilder(ClientInterface::class)->getMock();

        $facebook = new Facebook($client);
        $facebook->setConfig(['app_id' => 'foo', 'secret' => 'bar']);
        $request = $facebook->getRequest('http://www.heise.de');

        $this->assertEquals('graph.facebook.com', $request->getUri()->getHost());
        $this->assertEquals('/v7.0/', $request->getUri()->getPath());
        $this->assertEquals(
            'id=' . urlencode('http://www.heise.de') . '&fields=engagement&access_token=foo%7Cbar',
            $request->getUri()->getQuery()
        );
    }
}
