<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Core\Controller\Helper;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;

class ControllerActionExistsTest extends TestCase
{
    private MockObject|ContainerInterface $container;
    private ControllerActionExists $controllerActionExists;

    protected function setUp(): void
    {
        parent::setUp();

        $this->container = $this->createMock(ContainerInterface::class);

        $this->controllerActionExists = new ControllerActionExists(
            $this->container
        );
    }

    /**
     * @dataProvider provider
     */
    public function testControllerActionExists(string $path, bool $expected, string $hasMethodPath, bool $hasMethodResult): void
    {
        $this->container->method('has')
            ->with($hasMethodPath)
            ->willReturn($hasMethodResult);

        self::assertSame($expected, $this->controllerActionExists->controllerActionExists($path));
    }

    /**
     * @return mixed[][]
     */
    public function provider(): array
    {
        return [
            ['', false, '', false],
            ['news', false, '', false],
            ['acp/news', true, 'news.controller.acp.index.index', true],
            ['acp/news/index', true, 'news.controller.acp.index.index', true],
            ['acp/news/index/index', true, 'news.controller.acp.index.index', true],
            ['acp/news/index/index_post', true, 'news.controller.acp.index.index_post', true],
        ];
    }
}
