<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Share\Shariff;

use Http\Adapter\Guzzle6\Client;
use PHPUnit\Framework as PHPUnit;
use Psr\Log\LoggerInterface;
use Symfony\Component\Cache\Adapter\NullAdapter;

class BackendTest extends PHPUnit\TestCase
{
    /***
     * @var string[]
     */
    private array $services = [
        // "Facebook",
        // "Flattr",
        'Pinterest',
        'Reddit',
        // "StumbleUpon",
        'Xing',
        'Buffer',
        'Vk',
    ];

    public function testShariff(): void
    {
        $shariff = new Backend(
            [
                'domains' => ['www.heise.de'],
                'cache' => ['ttl' => 1],
                'services' => $this->services,
            ],
            new Client(),
            new NullAdapter(),
            $this->createMock(LoggerInterface::class)
        );

        $counts = $shariff->get('https://www.heise.de');

        // $this->assertArrayHasKey('flattr', $counts);
        if (\array_key_exists('flattr', $counts)) {
            $this->assertIsInt($counts['flattr']);
            $this->assertGreaterThanOrEqual(0, $counts['flattr']);
        }

        // $this->assertArrayHasKey('pinterest', $counts);
        if (\array_key_exists('pinterest', $counts)) {
            $this->assertIsInt($counts['pinterest']);
            $this->assertGreaterThanOrEqual(0, $counts['pinterest']);
        }

        // $this->assertArrayHasKey('stumbleupon', $counts);
        if (\array_key_exists('stumbleupon', $counts)) {
            $this->assertIsInt($counts['stumbleupon']);
            $this->assertGreaterThanOrEqual(0, $counts['stumbleupon']);
        }

        // $this->assertArrayHasKey('xing', $counts);
        if (\array_key_exists('xing', $counts)) {
            $this->assertIsInt($counts['xing']);
            $this->assertGreaterThanOrEqual(0, $counts['xing']);
        }

        // $this->assertArrayHasKey('reddit', $counts);
        if (\array_key_exists('reddit', $counts)) {
            $this->assertIsInt($counts['reddit']);
            $this->assertGreaterThanOrEqual(0, $counts['reddit']);
        }

        // $this->assertArrayHasKey('buffer', $counts);
        if (\array_key_exists('buffer', $counts)) {
            $this->assertIsInt($counts['buffer']);
            $this->assertGreaterThanOrEqual(0, $counts['buffer']);
        }

        // $this->assertArrayHasKey('vk', $counts);
        if (\array_key_exists('vk', $counts)) {
            $this->assertIsInt($counts['vk']);
            $this->assertGreaterThanOrEqual(0, $counts['vk']);
        }
    }

    public function testInvalidDomain(): void
    {
        $shariff = new Backend(
            [
                'domains' => ['www.heise.de'],
                'cache' => ['ttl' => 0],
                'services' => $this->services,
            ],
            new Client(),
            new NullAdapter(),
            $this->createMock(LoggerInterface::class)
        );

        $counts = $shariff->get('https://example.com');

        $this->assertNull($counts);
    }
}
