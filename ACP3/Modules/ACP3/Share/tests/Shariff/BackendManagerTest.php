<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Share\Shariff;

use ACP3\Modules\ACP3\Share\Shariff\Backend\Buffer;
use ACP3\Modules\ACP3\Share\Shariff\Backend\Vk;
use Http\Adapter\Guzzle6\Client;
use PHPUnit\Framework as PHPUnit;
use Psr\Log\LoggerInterface;
use Symfony\Component\Cache\Adapter\NullAdapter;

class BackendManagerTest extends PHPUnit\TestCase
{
    private BackendManager $backendManager;

    protected function setUp(): void
    {
        parent::setUp();

        $services = [
            'buffer' => new Buffer(),
        ];

        $this->backendManager = new BackendManager(
            'testCacheKey',
            new NullAdapter(),
            new Client(),
            $this->createMock(LoggerInterface::class),
            ['www.heise.de'],
            $services,
        );
    }

    public function testShariff(): void
    {
        $counts = $this->backendManager->get('https://www.heise.de');

        $this->assertIsInt($counts['buffer']);
        $this->assertGreaterThanOrEqual(0, $counts['buffer']);
    }

    public function testInvalidDomain(): void
    {
        $counts = $this->backendManager->get('https://example.com');

        $this->assertNull($counts);
    }
}
