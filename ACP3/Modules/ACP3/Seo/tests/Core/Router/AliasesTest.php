<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Seo\Core\Router;

use ACP3\Core\Modules;
use ACP3\Modules\ACP3\Seo\Services\SeoInformationService;
use PHPUnit\Framework\TestCase;

class AliasesTest extends TestCase
{
    /**
     * @var Aliases
     */
    private $aliases;
    /**
     * @var \PHPUnit\Framework\MockObject\MockObject&SeoInformationService
     */
    private $seoInformationServiceMock;
    /**
     * @var \PHPUnit\Framework\MockObject\MockObject&Modules
     */
    private $modulesMock;

    protected function setup(): void
    {
        $this->seoInformationServiceMock = $this->createMock(SeoInformationService::class);
        $this->modulesMock = $this->createMock(Modules::class);

        $this->modulesMock->expects(self::once())
            ->method('isInstalled')
            ->willReturn(true);

        $this->aliases = new Aliases($this->modulesMock, $this->seoInformationServiceMock);
    }

    /**
     * @dataProvider uriAliasDataProvider()
     *
     * @param array<string, array<string, string>> $expectedAliasCache
     */
    public function testGetUriAlias(string $path, string $uriAlias, bool $emptyOnNoResult, array $expectedAliasCache): void
    {
        $this->setUpSeoCacheExpectations($expectedAliasCache);

        self::assertEquals($uriAlias, $this->aliases->getUriAlias($path, $emptyOnNoResult));
    }

    /**
     * @return mixed[]
     */
    public static function uriAliasDataProvider(): array
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

    /**
     * @param array<string, array<string, mixed>> $expectedReturn
     */
    private function setUpSeoCacheExpectations(array $expectedReturn): void
    {
        $this->seoInformationServiceMock
            ->expects(self::once())
            ->method('getAllSeoInformation')
            ->willReturn($expectedReturn);
    }

    public function testUriAliasExistsNoAliasExists(): void
    {
        $this->setUpSeoCacheExpectations([]);

        $path = 'foo/bar/baz';
        self::assertFalse($this->aliases->uriAliasExists($path));
    }

    public function testUriAliasExistsAliasExists(): void
    {
        $this->setUpSeoCacheExpectations([
            'foo/bar/baz/' => [
                'alias' => 'lorem-ipsum-dolor',
            ],
        ]);

        $path = 'foo/bar/baz';
        self::assertTrue($this->aliases->uriAliasExists($path));
    }
}
