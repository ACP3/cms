<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Core\Assets\FileResolver;

use ACP3\Core\Authentication\Model\UserModelInterface;
use ACP3\Core\Controller\AreaEnum;
use ACP3\Core\Environment\AreaMatcher;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;

class MinifiedAwareFileCheckerStrategyTest extends TestCase
{
    private MinifiedAwareFileCheckerStrategy $strategy;
    private AreaMatcher&MockObject $areaMatcherMock;
    private UserModelInterface&MockObject $userModelMock;
    private StraightFileCheckerStrategy&MockObject $straightFileStrategyMock;

    protected function setUp(): void
    {
        parent::setUp();

        $requestStackMock = $this->createMock(RequestStack::class);
        $requestStackMock->method('getMainRequest')
            ->willReturn($this->createMock(Request::class));
        $this->areaMatcherMock = $this->createMock(AreaMatcher::class);
        $this->userModelMock = $this->createMock(UserModelInterface::class);
        $this->straightFileStrategyMock = $this->createMock(StraightFileCheckerStrategy::class);

        $this->strategy = new MinifiedAwareFileCheckerStrategy(
            $requestStackMock,
            $this->areaMatcherMock,
            $this->userModelMock,
            $this->straightFileStrategyMock
        );
    }

    public function testFindResourceInAdmin(): void
    {
        $this->areaMatcherMock->method('getAreaFromRequest')
            ->willReturn(AreaEnum::AREA_ADMIN);
        $this->straightFileStrategyMock->expects(self::once())
            ->method('findResource')
            ->with('/admin-foo.min.css')
            ->willReturn('/admin-foo.min.css');

        self::assertEquals('/admin-foo.min.css', $this->strategy->findResource('/foo.css'));
    }

    public function testFindResourceAuthenticated(): void
    {
        $this->areaMatcherMock->method('getAreaFromRequest')
            ->willReturn(AreaEnum::AREA_FRONTEND);
        $this->userModelMock->method('isAuthenticated')
            ->willReturn(true);
        $this->straightFileStrategyMock->expects(self::once())
            ->method('findResource')
            ->with('/logged-in-foo.min.css')
            ->willReturn('/logged-in-foo.min.css');

        self::assertEquals('/logged-in-foo.min.css', $this->strategy->findResource('/foo.css'));
    }

    public function testFindResourceSimple(): void
    {
        $this->areaMatcherMock->method('getAreaFromRequest')
            ->willReturn(AreaEnum::AREA_FRONTEND);
        $this->userModelMock->method('isAuthenticated')
            ->willReturn(false);
        $this->straightFileStrategyMock->expects(self::once())
            ->method('findResource')
            ->with('/foo.min.css')
            ->willReturn('/foo.min.css');

        self::assertEquals('/foo.min.css', $this->strategy->findResource('/foo.css'));
    }

    public function testIsAllowed(): void
    {
        self::assertTrue($this->strategy->isAllowed('/foo.css'));
        self::assertTrue($this->strategy->isAllowed('/foo.js'));
        self::assertFalse($this->strategy->isAllowed('/foo.min.css'));
        self::assertFalse($this->strategy->isAllowed('/foo.min.js'));
        self::assertFalse($this->strategy->isAllowed('/foo.svg'));
    }
}
