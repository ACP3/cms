<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Share\Shariff;

use ACP3\Modules\ACP3\Share\Shariff\Backend\ServiceInterface;
use PHPUnit\Framework as PHPUnit;

class SocialSharingBackendServiceLocatorTest extends PHPUnit\TestCase
{
    private SocialSharingBackendServiceLocator $locator;

    protected function setUp(): void
    {
        parent::setUp();

        $this->locator = new SocialSharingBackendServiceLocator();
    }

    public function testSetConfig(): void
    {
        /** @var ServiceInterface & PHPUnit\MockObject\MockObject $mockService */
        $mockService = $this->createMock(ServiceInterface::class);
        $mockService->method('getName')->willReturn('mockservice');
        $mockService->expects($this->once())
            ->method('setConfig')
            ->with(['foo' => 'bar']);

        $this->locator->registerService($mockService);

        $services = $this->locator->getServicesByName(
            ['mockservice'],
            ['mockservice' => ['foo' => 'bar']]
        );
        $this->assertCount(1, $services);
    }

    public function testConfigNotSet(): void
    {
        /** @var ServiceInterface & PHPUnit\MockObject\MockObject $mockService */
        $mockService = $this->createMock(ServiceInterface::class);
        $mockService->method('getName')->willReturn('mockservice');
        $mockService->expects($this->never())->method('setConfig');

        $this->locator->registerService($mockService);

        $services = $this->locator->getServicesByName(
            ['mockservice'],
            ['otherservice' => ['foo' => 'bar']]
        );
        $this->assertCount(1, $services);
    }
}
