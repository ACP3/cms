<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Share\Shariff\Backend;

use GuzzleHttp\ClientInterface;
use PHPUnit\Framework as PHPUnit;

/**
 * Class ServiceFactoryTest.
 */
class ServiceFactoryTest extends PHPUnit\TestCase
{
    public function testSetConfig()
    {
        /** @var ServiceInterface|PHPUnit\MockObject\MockObject $mockService */
        $mockService = $this->getMockBuilder(ServiceInterface::class)->getMock();

        $mockService->expects($this->once())
            ->method('setConfig')
            ->with(['foo' => 'bar'])
        ;

        /** @var ClientInterface|PHPUnit\MockObject\MockObject $mockClient */
        $mockClient = $this->getMockBuilder(ClientInterface::class)->getMock();

        $serviceFactory = new ServiceFactory($mockClient);
        $serviceFactory->registerService('MockService', $mockService);

        $services = $serviceFactory->getServicesByName(
            ['MockService'],
            ['MockService' => ['foo' => 'bar']]
        );
        $this->assertCount(1, $services);
    }

    public function testConfigNotSet()
    {
        /** @var ServiceInterface|PHPUnit\MockObject\MockObject $mockService */
        $mockService = $this->getMockBuilder(ServiceInterface::class)->getMock();

        $mockService->expects($this->never())->method('setConfig');

        /** @var ClientInterface|PHPUnit\MockObject\MockObject $mockClient */
        $mockClient = $this->getMockBuilder(ClientInterface::class)->getMock();

        $serviceFactory = new ServiceFactory($mockClient);
        $serviceFactory->registerService('MockService', $mockService);

        $services = $serviceFactory->getServicesByName(
            ['MockService'],
            ['OtherService' => ['foo' => 'bar']]
        );
        $this->assertCount(1, $services);
    }
}
