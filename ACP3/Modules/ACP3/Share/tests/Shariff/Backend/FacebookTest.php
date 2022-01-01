<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Share\Shariff\Backend;

use PHPUnit\Framework as PHPUnit;

/**
 * Class FacebookTest.
 */
class FacebookTest extends PHPUnit\TestCase
{
    public function testConfig(): void
    {
        $facebook = new Facebook();
        $facebook->setConfig(['app_id' => 'foo', 'secret' => 'bar']);
        $request = $facebook->getRequest('https://www.heise.de');
        $this->assertEquals(
            'id=' . urlencode('https://www.heise.de') . '&fields=og_object%7Bengagement%7D&access_token=foo%7Cbar',
            $request->getUri()->getQuery()
        );
    }

    public function testUsesGraphApi(): void
    {
        $facebook = new Facebook();
        $facebook->setConfig(['app_id' => 'foo', 'secret' => 'bar']);
        $request = $facebook->getRequest('https://www.heise.de');

        $this->assertEquals('graph.facebook.com', $request->getUri()->getHost());
        $this->assertEquals('/v12.0/', $request->getUri()->getPath());
        $this->assertEquals(
            'id=' . urlencode('https://www.heise.de') . '&fields=og_object%7Bengagement%7D&access_token=foo%7Cbar',
            $request->getUri()->getQuery()
        );
    }
}
