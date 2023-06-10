<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Core\Helpers;

use Cocur\Slugify\Slugify;

class StringFormatterTest extends \PHPUnit\Framework\TestCase
{
    private static string $shortenEntryText = <<<HTML
<p>It looks like that the installation of the ACP3 CMS was successful.<br>
This is just a test news, you can now edit or delete in the administration area.</p>
<div class="panel panel-default">
    <div class="panel-heading">
        <h3 class="panel-title">Aktuelle Downloads</h3>
    </div>
    <div class="list-group">
        <a href="/acp3/cms/index.php/files/index/details/id_1/" class="list-group-item" title="06.03.16, 19:21 - Test">Test</a>
    </div>
</div>
HTML;

    protected StringFormatter $stringFormatter;

    protected function setup(): void
    {
        $this->stringFormatter = new StringFormatter(new Slugify());
    }

    /**
     * @return string[][]
     */
    public static function makeStringUrlSafeDataProvider(): array
    {
        return [
            'german_umlauts' => ['äüöumß', 'aeueoeumss'],
            'german_umlauts_source_entities' => ['&auml;&ouml;&szlig;', 'aeoess'],
            'underscore' => ['foo_bar', 'foo-bar'],
            'complex_characters' => ['ピックアップ', ''],
            'preserve_numbers' => ['ピ23ッ6ク7アップ', '23-6-7'],
            'to_lower_case' => ['ÄÜÖumß', 'aeueoeumss'],
        ];
    }

    /**
     * @dataProvider makeStringUrlSafeDataProvider
     */
    public function testMakeStringUrlSafe(string $value, string $expected): void
    {
        self::assertEquals($expected, $this->stringFormatter->makeStringUrlSafe($value));
    }

    /**
     * @return mixed[]
     */
    public static function nl2pDataProvider(): array
    {
        return [
            'single_line' => [
                'Foo Bar Baz',
                false,
                '<p>Foo Bar Baz</p>',
            ],
            'single_line_line_breaks' => [
                'Foo Bar Baz',
                true,
                '<p>Foo Bar Baz</p>',
            ],
            'multi_line' => [
                "Foo\nBar\nBaz",
                false,
                "<p>Foo</p>\n<p>Bar</p>\n<p>Baz</p>",
            ],
            'multi_line_line_breaks' => [
                "Foo\nBar\nBaz",
                true,
                '<p>Foo<br>Bar<br>Baz</p>',
            ],
            'multi_empty_line_line_breaks' => [
                "Foo\n\nBar\n\nBaz\nLorem",
                true,
                "<p>Foo</p>\n<p>Bar</p>\n<p>Baz<br>Lorem</p>",
            ],
        ];
    }

    /**
     * @dataProvider nl2pDataProvider
     */
    public function testNl2p(string $value, bool $useLineBreaks, string $expected): void
    {
        self::assertEquals($expected, $this->stringFormatter->nl2p($value, $useLineBreaks));
    }

    /**
     * @return mixed[]
     */
    public static function shortenEntryDataProvider(): array
    {
        return [
            'empty' => [
                '',
                0,
                '',
                '',
            ],
            'shorten_text_without_offset' => [
                self::$shortenEntryText,
                0,
                '',
                'It looks like that the installation of the ACP3 CMS was successful.
This is just a test news, you ca',
            ],
            'shorten_text_with_offset' => [
                self::$shortenEntryText,
                10,
                '',
                'It looks like that the installation of the ACP3 CMS was successful.
This is just a test ne',
            ],
            'append_text' => [
                self::$shortenEntryText,
                0,
                '...',
                'It looks like that the installation of the ACP3 CMS was successful.
This is just a test news, you ca...',
            ],
        ];
    }

    /**
     * @dataProvider shortenEntryDataProvider
     */
    public function testShortenEntry(string $value, int $offset, string $append, string $expected): void
    {
        self::assertEquals($expected, $this->stringFormatter->shortenEntry($value, 100, $offset, $append));
    }

    public function testShortenEntryInvalidArgumentException(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('The offset should not be bigger then the to be displayed characters.');

        $this->stringFormatter->shortenEntry(self::$shortenEntryText, 50, 100);
    }
}
