<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Seo\Core\Router;

use ACP3\Core\Modules;
use ACP3\Modules\ACP3\Seo\Cache;

class AliasesTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var \ACP3\Modules\ACP3\Seo\Core\Router\Aliases
     */
    private $aliases;
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    private $seoCacheMock;
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    private $modulesMock;

    protected function setUp()
    {
        $this->seoCacheMock = $this->createMock(Cache::class);
        $this->modulesMock = $this->createMock(Modules::class);

        $this->modulesMock->expects($this->once())
            ->method('isActive')
            ->willReturn(true);

        $this->aliases = new Aliases($this->modulesMock, $this->seoCacheMock);
    }

    /**
     * @dataProvider uriAliasDataProvider()
     */
    public function testGetUriAlias(string $path, string $uriAlias, bool $emptyOnNoResult, array $expectedAliasCache)
    {
        $this->setUpSeoCacheExpectations($expectedAliasCache);

        $this->assertEquals($uriAlias, $this->aliases->getUriAlias($path, $emptyOnNoResult));
    }

    public function uriAliasDataProvider(): array
    {
        return [
            'empty_aliases_cache' => [
                'foo/bar/baz',
                'foo/bar/baz/',
                false,
                [],
            ],
            'empty_alias' => [
                'foo/bar/baz',
                'foo/bar/baz/',
                false,
                [
                    'foo/bar/baz/' => [
                        'alias' => '',
                    ],
                ],
            ],
            'empty_aliases_cache_empty_on_no_result' => [
                'foo/bar/baz',
                '',
                true,
                [],
            ],
            'found_uri_alias' => [
                'foo/bar/baz',
                'lorem-ipsum-dolor',
                true,
                [
                    'foo/bar/baz/' => [
                        'alias' => 'lorem-ipsum-dolor',
                    ],
                ],
            ],
        ];
    }

    private function setUpSeoCacheExpectations(array $expectedReturn)
    {
        $this->seoCacheMock
            ->expects($this->once())
            ->method('getCache')
            ->willReturn($expectedReturn);
    }

    public function testUriAliasExistsNoAliasExists()
    {
        $this->setUpSeoCacheExpectations([]);

        $path = 'foo/bar/baz';
        $this->assertFalse($this->aliases->uriAliasExists($path));
    }

    public function testUriAliasExistsAliasExists()
    {
        $this->setUpSeoCacheExpectations([
            'foo/bar/baz/' => [
                'alias' => 'lorem-ipsum-dolor',
            ],
        ]);

        $path = 'foo/bar/baz';
        $this->assertTrue($this->aliases->uriAliasExists($path));
    }
}
