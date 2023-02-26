<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Core\SEO;

use ACP3\Core\Controller\AreaEnum;
use ACP3\Core\Http\RequestInterface;
use ACP3\Core\Router\RouterInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class MetaStatementsServiceTest extends TestCase
{
    private MetaStatementsService $metaStatementsService;

    private RequestInterface|MockObject $requestMock;

    private RouterInterface|MockObject $routerMock;

    protected function setUp(): void
    {
        parent::setUp();

        $this->requestMock = $this->createMock(RequestInterface::class);
        $this->routerMock = $this->createMock(RouterInterface::class);
        $this->metaStatementsService = new MetaStatementsService(
            $this->requestMock,
            $this->routerMock,
        );
    }

    /**
     * @dataProvider canonicalDataProvider
     */
    public function testCanonical(string $expected, AreaEnum $area, string $route): void
    {
        $this->requestMock->expects(self::atLeastOnce())
            ->method('getArea')
            ->willReturn($area);
        $this->requestMock->expects(self::any())
            ->method('getQuery')
            ->willReturn($route);
        $this->routerMock->expects(self::any())
            ->method('route')
            ->willReturn('https://localhost/' . $route);

        self::assertSame($expected, $this->metaStatementsService->getMetaTags()['canonical']);
    }

    /**
     * @return array<string, mixed[]>
     */
    public function canonicalDataProvider(): array
    {
        return [
            'empty-for-admin' => ['', AreaEnum::AREA_ADMIN, 'acp/foo/bar/baz/'],
            'empty-for-errors' => ['', AreaEnum::AREA_ADMIN, 'errors/index/index/'],
            'self-referencing' => ['https://localhost/foo/bar/baz/', AreaEnum::AREA_FRONTEND, 'foo/bar/baz/'],
        ];
    }
}
