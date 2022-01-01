<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Share\Shariff\Backend;

use PHPUnit\Framework as PHPUnit;

class ServiceFactoryTest extends PHPUnit\TestCase
{
    public function testSetConfig(): void
    {
        /** @var ServiceInterface & PHPUnit\MockObject\MockObject $mockService */
        $mockService = $this->createMock(ServiceInterface::class);

        $mockService->expects($this->once())
            ->method('setConfig')
            ->with(['foo' => 'bar'])
        ;

        $serviceFactory = new ServiceFactory();
        $serviceFactory->registerService('MockService', $mockService);

        $services = $serviceFactory->getServicesByName(
            ['MockService'],
            ['MockService' => ['foo' => 'bar']]
        );
        $this->assertCount(1, $services);
    }

    public function testConfigNotSet(): void
    {
        /** @var ServiceInterface & PHPUnit\MockObject\MockObject $mockService */
        $mockService = $this->createMock(ServiceInterface::class);

        $mockService->expects($this->never())->method('setConfig');

        $serviceFactory = new ServiceFactory();
        $serviceFactory->registerService('MockService', $mockService);

        $services = $serviceFactory->getServicesByName(
            ['MockService'],
            ['OtherService' => ['foo' => 'bar']]
        );
        $this->assertCount(1, $services);
    }
}
