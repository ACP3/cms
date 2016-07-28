<?php
/**
 * Copyright (c) 2016 by the ACP3 Developers.
 * See the LICENCE file at the top-level module directory for licencing details.
 */

namespace ACP3\Core\Test\Helpers;


use ACP3\Core\Helpers\StringFormatter;

class StringFormatterTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var string
     */
    private $shortenEntryText = <<<HTML
<p>It looks like that the installation of the ACP3 4.0 was successful.<br>
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

    /**
     * @var StringFormatter
     */
    protected $stringFormatter;

    protected function setUp()
    {
        $this->stringFormatter = new StringFormatter();
    }

    public function makeStringUrlSafeDataProvider()
    {
        return [
            'german_umlauts' => ['äüöumß', 'aeueoeumss'],
            'german_umlauts_source_entities' => ['&auml;&ouml;&szlig;', 'aeoess'],
            'complex_characters' => ['ピックアップ', ''],
            'preserve_numbers' => ['ピ23ッ6ク7アップ', '2367'],
            'to_lower_case' => ['ÄÜÖumß', 'aeueoeumss'],
        ];
    }

    /**
     * @dataProvider makeStringUrlSafeDataProvider
     *
     * @param string $value
     * @param string $expected
     */
    public function testMakeStringUrlSafe($value, $expected)
    {
        $this->assertEquals($expected, $this->stringFormatter->makeStringUrlSafe($value));
    }

    public function nl2pDataProvider()
    {
        return [
            'single_line' => [
                'Foo Bar Baz',
                false,
                '<p>Foo Bar Baz</p>'
            ],
            'single_line_line_breaks' => [
                'Foo Bar Baz',
                true,
                '<p>Foo Bar Baz</p>'
            ],
            'multi_line' => [
                "Foo\nBar\nBaz",
                false,
                "<p>Foo</p>\n<p>Bar</p>\n<p>Baz</p>"
            ],
            'multi_line_line_breaks' => [
                "Foo\nBar\nBaz",
                true,
                "<p>Foo<br>Bar<br>Baz</p>"
            ],
            'multi_empty_line_line_breaks' => [
                "Foo\n\nBar\n\nBaz\nLorem",
                true,
                "<p>Foo</p>\n<p>Bar</p>\n<p>Baz<br>Lorem</p>"
            ],
        ];
    }

    /**
     * @dataProvider nl2pDataProvider
     *
     * @param string $value
     * @param bool   $useLineBreaks
     * @param string $expected
     */
    public function testNl2p($value, $useLineBreaks, $expected)
    {
        $this->assertEquals($expected, $this->stringFormatter->nl2p($value, $useLineBreaks));
    }

    public function shortenEntryDataProvider()
    {
        return [
            'empty' => [
                '',
                0,
                '',
                ''
            ],
            'shorten_text_without_offset' => [
                $this->shortenEntryText,
                0,
                '',
                'It looks like that the installation of the ACP3 4.0 was successful.
This is just a test news, you ca'
            ],
            'shorten_text_with_offset' => [
                $this->shortenEntryText,
                10,
                '',
                'It looks like that the installation of the ACP3 4.0 was successful.
This is just a test ne'
            ],
            'append_text' => [
                $this->shortenEntryText,
                0,
                '...',
                'It looks like that the installation of the ACP3 4.0 was successful.
This is just a test news, you ca...'
            ]
        ];
    }

    /**
     * @dataProvider shortenEntryDataProvider
     *
     * @param string $value
     * @param int    $offset
     * @param string $append
     * @param string $expected
     */
    public function testShortenEntry($value, $offset, $append, $expected)
    {
        $this->assertEquals($expected, $this->stringFormatter->shortenEntry($value, 100, $offset, $append));
    }

    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage The offset should not be bigger then the to be displayed characters.
     */
    public function testShortenEntryInvalidArgumentException()
    {
        $this->stringFormatter->shortenEntry($this->shortenEntryText, 50, 100);
    }
}
