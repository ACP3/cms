<?php

class AssetsTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var \ACP3\Core\Assets
     */
    private $assets;

    protected function setUp()
    {
        $this->assets = new \ACP3\Core\Assets();
    }

    public function testDefaultLibrariesEnabled()
    {
        $libraries = $this->assets->getEnabledLibrariesAsString();
        $this->assertEquals('jquery,bootstrap', $libraries);
    }

    public function testEnableDatepicker()
    {
        $this->assets->enableLibraries(['datetimepicker']);

        $libraries = $this->assets->getEnabledLibrariesAsString();
        $this->assertEquals('moment,jquery,bootstrap,datetimepicker', $libraries);
    }

    public function testFetchAdditionalThemeCssFiles()
    {
        $files = $this->assets->fetchAdditionalThemeCssFiles();

        $this->assertEquals(['additional-style.css'], $files);
    }

    public function testFetchAdditionalThemeJsFiles()
    {
        $files = $this->assets->fetchAdditionalThemeJsFiles();

        $this->assertEquals(['additional-script.js'], $files);
    }

}